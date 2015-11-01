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
use \Carbon\Carbon;

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

	protected $calculator; 
	/** 
	 * The active current monitors.
	 *
	 * Indexed by `$currentMonitor->appliance->id`.
	 * 
	 * @var \App\CurrentMonitor[] $activeMonitors The active current monitors
	 */
	private $activeMonitors;

	/** @var \App\Model\LoadCurve $aggregate The current aggregate curve */
	private $aggregate;

	/** @var \App\Model\LoadCurve[] $curves An array of LoadCurves indexed by `$appliance->id` */
	private $curves;

	/** @var \Carbon\Carbon $time The current time on which measurements are synchronized */
	private $time;

	private $demandHistory = null;

	/** @var \App\BufferedAnalog $bufferedAnalog Buffer to read from. */
	private $bufferedAnalog;

	public function __construct(CostCalculator $calculator) {
		$this->calculator = $calculator;
		$this->bufferedAnalog = new BufferedAnalog(2048, self::CHANNELS, 30, true);
		$this->time = Carbon::now()->second(0);
	}

	/**
	 * @inheritdoc
	 */
	public function appStart($appId) {
		$currentMonitor = AnalogCurrentMonitor::where('appliance_id', $appId);
		$this->activeMonitors[$appId] = $currentMonitor;
		$curve = Run::where('appliance_id', $appId)
			->and('is_running', true)->first()->loadCurve;
		$this->curves[$appId] = $curve;
	}

	/**
	 * @inheritdoc
	 */
	public function appStop($appId) {
		$curve = $this->curves[$appId];
		$curve->save();
		unset($this->activeMonitors[$appId]);
	}

	/**
	 * Take a reading from all monitors and store it in their respective curves.
	 *
	 * Also update the current demand history.
	 * 
	 * @return void
	 */
	public function measure() {
		$buffer = $this->bufferedAnalog->read();
		foreach ($this->activeMonitors as $appId => $monitor) {
			/* @var $monitor \App\Model\AnalogCurrentMonitor */
			foreach ($buffer[$monitor->ain_number] as $raw_value) {
				$watts = $monitor->getWatts($raw_value);
				$curve = $this->curves[$appId];
				$loadData = LoadData::create($monitor, $this->time, $watts);
				$curve->addToData($this->time, $loadData);
				$this->aggregate->addToData($this->time, $loadData);
			}


		}
	}

	public function meterLoop() {
		if ($this->demandHistory === null) {
			$this->demandHistory = new DemandHistory($this->calculator);
			$this->demandHistory->start();
		}
		while ($this->meterWait()) {
			$this->measure();
			$agg_load = $this->aggregate->getDataAt($this->time->timestamp);
			if ( ! $this->demandHistory->updateHistory($agg_load) ) {
				$this->demandHistory->complete();
				$this->demandHistory->save();
				$this->demandHistory = new DemandHistory($this->calculator);
				$this->demandHistory->start();
				$this->demandHistory->updateHistory($agg_load);
			}
		}
	}

	public function meterWait() {
		$this->time = $this->time->copy()->addMinute();
		return time_sleep_until(time() + 1);
	}
}
