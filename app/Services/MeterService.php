<?php

/**
 * An implementation of the Meter service interface.
 */
namespace App\Services;

use App\AnalogCurrentMonitor;
use App\LoadCurve;
use App\Services\CostCalculator;
use App\DemandHistory;
use App\Run;

/**
 * An implementation of the Meter service interface.
 */
class MeterService implements Meter
{
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

	/** @var \App\LoadCurve $aggregate The current aggregate curve */
	private $aggregate;

	/** @var \App\LoadCurve[] $curves An array of LoadCurves indexed by `$appliance->id` */
	private $curves;

	/** @var int $time The current time on which measurements are synchronized */
	private $time;

	private $demandHistory = null;

	public function __construct(CostCalculator $calculator) {
		$this->calculator = $calculator;
	}

	/**
	 * @inheritdoc
	 */
	public function appStart($appId) {
		$currentMonitor = AnalogCurrentMonitor::where('appliance_id', $appId);
		$currentMonitor->setup();
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
		$curve->serialize_data();
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
		foreach ($this->activeMonitors as $appId => $monitor) {
			$watts = $monitor->getWatts();
			$curve = $this->curves[$appId];
			$curve->addToData($this->time, $watts);
			$this->aggregate->addToData($this->time, $watts);
		}
	}

	public function meterLoop() {
		if ($this->demandHistory === null) {
			$this->demandHistory = new DemandHistory($this->calculator);
			$this->demandHistory->start();
		}
		while ($this->meterWait()) {
			$this->measure();
			$agg_watts = $this->aggregate->getDataAt($this->time);
			if ( ! $this->demandHistory->updateHistory($this->time, $agg_watts) ) {
				$this->demandHistory->complete();
				$this->demandHistory->save();
				$this->demandHistory = new DemandHistory($this->calculator);
				$this->demandHistory->start();
				$this->demandHistory->updateHistory($this->time, $agg_watts);
			}
		}
	}

	public function meterWait() {
		$this->time = time() + 1;
		return time_sleep_until($this->time);
	}
}
