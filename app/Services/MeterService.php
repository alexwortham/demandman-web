<?php

/**
 * An implementation of the Meter service interface.
 */
namespace App\Services;

use App\BufferedAnalog;
use App\Model\AnalogCurrentMonitor;
use App\Model\LoadData;
use App\Model\DemandHistory;
use App\Model\Run;
use App\Model\LoadCurve;
use \Carbon\Carbon;
use \ErrorException;
use Redis;

/**
 * An implementation of the Meter service interface.
 */
class MeterService implements Meter
{
	/**
	 * Which analog channels to open for buffering.
	 *
	 * This is the result of CHAN_0 | CHAN_1 | CHAN_2 | CHAN_3 constants
	 * found in the BufferedAnalog class.
	 */
	const CHANNELS = 15;

	/** @var int $meter_interval The number of seconds between readings. */
	public static $meter_interval = 1;

	/**
	 * An event that needs to be processed by the meter.
	 *
	 * If not NULL an event has been set by a signal handler and needs to
	 * be processed by handle_signal().
	 *
	 * @var mixed $event An event that needs to be processed by the meter.
	 */
	private static $event = NULL;

	/** @var CostCalculator $calculator CostCalculator used for calculating
	 * energy costs. */
	protected $calculator; 
	/** 
	 * The active current monitors.
	 *
	 * Indexed by `$currentMonitor->appliance->id`.
	 * 
	 * @var \App\Model\AnalogCurrentMonitor[] $activeMonitors The active current monitors
	 */
	private $activeMonitors;

	/** @var \App\Model\LoadCurve $aggregate The current aggregate curve */
	private $aggregate;

	/** @var \App\Model\LoadCurve[] $curves An array of LoadCurves indexed by `$appliance->id` */
	private $curves;

	/** @var \Carbon\Carbon $time The current time on which measurements are synchronized */
	private $time;

	/** @var \Carbon\Carbon $time The previous time on which measurements were made */
	private $prev_time;

	/**
	 * @var \App\Model\DemandHistory $demandHistory The current DemandHistory
	 * object being used for tracking demand history.
	 */
	private $demandHistory = null;

	/** @var \App\BufferedAnalog $bufferedAnalog Buffer to read from. */
	private $bufferedAnalog;

	private $redis;

	/**
	 * @param CostCalculator $calculator Injected by Laravel.
	 */
	public function __construct(CostCalculator $calculator) {
		$this->calculator = $calculator;
		$this->bufferedAnalog = new BufferedAnalog(2048, self::CHANNELS, 30, true);
		$this->curves = array();
		for ($c = 1, $i = 0; $c <= 128; $c *= 2, $i++) {
			if (($c & self::CHANNELS) > 0) {
				$cm = AnalogCurrentMonitor::where('ain_number', $i)->first();
				$this->activeMonitors[$i] = $cm;
				$this->curves[$i] = new LoadCurve();
				//set stuff on curve?
			}
		}
		$this->time = Carbon::now()->second(0);
		$this->prev_time = $this->time->copy()->subMinute();
		$this->aggregate = new LoadCurve();
		$this->redis = Redis::connection('pubsub');
	}

	/**
	 * @inheritdoc
	 */
	public function appStart($appId) {
		$currentMonitor = AnalogCurrentMonitor::where('appliance_id', $appId)->first();
		$currentMonitor = $this->activeMonitors[$currentMonitor->ain_number];
		$currentMonitor->is_active = true;
		$curve = $this->curves[$currentMonitor->ain_number];
		$curve->save();
		$run = new Run();
		$run->is_running = true;
		$run->appliance_id = $appId;
		$run->loadCurve()->associate($curve);
		$run->save();
	}

	/**
	 * @inheritdoc
	 */
	public function appStop($appId) {
		$currentMonitor = AnalogCurrentMonitor::where('appliance_id', $appId)->first();
		$currentMonitor = $this->activeMonitors[$currentMonitor->ain_number];
		$currentMonitor->is_active = false;
		$curve = $this->curves[$currentMonitor->ain_number];
		/* @var $run \App\Model\Run */
		$run = Run::where('appliance_id', $appId)
			->where('is_running', true)->first();
		//$run->loadCurve()->associate($curve);
		$run->is_running = false;
		$run->save();
		$this->curves[$currentMonitor->ain_number] = new LoadCurve();
		//set stuff on curve?
	}

	/**
	 * Take a reading from all monitors and store it in their respective curves.
	 *
	 * Also update the current demand history.
	 * 
	 * @return void
	 */
	public function measure() {
		//read a single averaged value for each channel.
		$buffer = $this->bufferedAnalog->read(true);
		if (!is_array($buffer)) {
			fprintf(STDERR, "%s\n", $buffer);
			return;
		}
		foreach ($this->activeMonitors as $ain_number => $monitor) {
			/* @var $monitor \App\Model\AnalogCurrentMonitor */
			//printf("aggregate->setDataAt(%d)\n", $this->time->timestamp);
			$this->prev_time = $this->time->copy()->subMinute();
			$this->aggregate->setDataAt($this->prev_time, LoadData::createLD(NULL, NULL, $this->time, 0));
			foreach ($buffer[$monitor->ain_number] as $raw_value) {
				$watts = $monitor->getWatts($raw_value);
				if ($monitor->is_active === true) {
					//printf("AIN%d: raw = %.4f; calc = %.4f\n", $ain_number, $raw_value, $watts);
					$curve = $this->curves[$ain_number];
					$loadData = LoadData::createLD($monitor, $curve, $this->time, $watts);
					$curve->setDataAt($this->prev_time,	$loadData);
					$this->aggregate->addToData($this->prev_time, $loadData);
					if ($curve->id !== NULL && is_array($curve->load_data) && count($curve->load_data) > 0) {
						$curve->loadData()->saveMany($curve->load_data);
						$curve->load_data = array();
					}
				}
			}


		}
	}

	/**
	 * @inheritdoc
	 */
	public function meterLoop() {
		$this->bufferedAnalog->open();
		sleep(1); //give the buffer a second to fill
		$this->measureBiases();
		if ($this->demandHistory === null) {
			$this->demandHistory = new DemandHistory($this->calculator);
			$this->demandHistory->start($this->time);
		}
		while ($this->meterWait()) {
			pcntl_signal_dispatch();
            if (self::$event !== NULL) {
				echo "Meter loop caught event, calling handle_signal()\n".
                $this->handle_signal();
            }
			$this->measure();
			$agg_data = $this->aggregate->getDataAt($this->prev_time->timestamp);
			if ( ! $this->demandHistory->updateHistory($agg_data) ) {
				$this->demandHistory->complete();
				$this->demandHistory->save();
				$this->demandHistory = new DemandHistory($this->calculator);
				$this->demandHistory->start($this->time);
			}
		}
		$this->bufferedAnalog->close();
	}

	public function measureBiases() {
		$bias_avgs = array();
		foreach ($this->activeMonitors as $ain_number => $monitor) {
			$bias_avgs[$ain_number] = 0;
		}
		$num_samples = 5;
		for ($i = 0; $i < $num_samples; $i++) {

            $buffer = $this->bufferedAnalog->read(true);
            if (!is_array($buffer)) {
                fprintf(STDERR, "%s\n", $buffer);
                return;
            }
            foreach ($this->activeMonitors as $ain_number => $monitor) {
                /* @var $monitor \App\Model\AnalogCurrentMonitor */
				foreach ($buffer[$monitor->ain_number] as $raw_value) {
					$bias_avgs[$monitor->ain_number] += $raw_value;
				}
            }
			time_sleep_until(time() + 1);
		}
		foreach ($bias_avgs as $ain_number => $sum) {
			$monitor = $this->activeMonitors[$ain_number];
			$monitor->bias = $sum / $num_samples;
			$monitor->save();
		}
	}

	/**
	 * Wait for 1 second.
	 *
	 * Updates `$this->prev_time` and `$this->time` appropriately.
	 *
	 * @return bool Returns the result of time_sleep_until().
	 */
	public function meterWait() {
		$this->prev_time = $this->time;
		$this->time = $this->time->copy()->addMinute();
		$this->redis->set('simulation:time', $this->time);
		return time_sleep_until(time() + 1);
	}

	/**
	 * @inheritdoc
	 */
	public function setEvent($event) {
		self::$event = $event;
	}

	/**
	 * Process any events detected by meterLoop().
	 *
	 * @return void
	 */
	private function handle_signal() {

		//do stuff to handle simulation changes
		$event = json_decode(json_decode(self::$event, true), true);
		$action = ucfirst($event['data']['actionResponse']['action']);
		$appId = $event['data']['actionResponse']['appId'];
        $status = $event['data']['actionResponse']['status'];

        //only execute if status is success?
		try {
			if ($status === "approved" || $status === "successful") {
				printf("MeterService will call \$this -> app$action(%d)\n", $appId);
				call_user_func_array(array($this, "app$action"),
					array($appId));
			}
		} catch (ErrorException $e) {
			printf("%s\n%s\n", $e->getMessage(), $e->getTraceAsString());
		}
		self::$event = NULL;
	}
}
