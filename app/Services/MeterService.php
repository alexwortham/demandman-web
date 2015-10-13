<?php

/**
 * An implementation of the Meter service interface.
 */
namespace App\Services;

use App\Services\CostCalculator;
use App\DemandHistory;

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
	 * @var App\CurrentMonitor[] $activeMonitors The active current monitors
	 */
	private $activeMonitors;

	/** @var App\LoadCurve $aggregate The current aggregate curve */
	private $aggregate;

	/** @var App\LoadCurve[] $curves An array of LoadCurves indexed by `$appliance->id` */
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
		//get current monitor from database and call setup()
		//put it in $activeMonitors and create a new LoadCurve
		//put the curve in $curves and return
	}

	/**
	 * @inheritdoc
	 */
	public function appStop($appId) {
		//call $curve->save(), remove monitor from active monitors
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
			($this->curves[$appId])->appendToData($this->time, $watts);
			$this->aggregate->addToData($this->time, $watts);
		}
	}

	public function meterLoop() {
		if ($this->demandHistory === null) {
			$this->demandHistory = new DemandHistory();
			$this->demandHistory->start();
		}
		while ($this->meterWait()) {
			$this->measure();
			$agg_watts = $this->aggregate->getDataAt($this->time);
			if ( ! $this->demandHistory->updateHistory($agg_watts) ) {
				$this->demandHistory->complete();
				$this->demandHistory->save();
				$this->demandHistory = new DemandHistory();
				$this->demandHistory->start();
				$this->demandHistory->updateHistory($agg_watts);
			}
		}
	}

	public function meterWait() {
		$this->time = time() + 1;
		return time_sleep_until($this->time);
	}
}
