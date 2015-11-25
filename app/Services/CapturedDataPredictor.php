<?php

/**
 * An implementation of the Predictor interface.
 *
 * Uses captured data to predict future usage.
 */
namespace App\Services;

use App\Model\Appliance;
use App\Model\LoadCurve;
use App\Model\LoadData;
use App\Model\Run;
use \Carbon\Carbon;
use App\Model\DemandHistory;
use App\Services\CostCalculator;

/**
 * An implementation of the Predictor interface.
 *
 * Uses captured data to predict future usage.
 */
class CapturedDataPredictor implements Predictor
{
	/** @var \App\Services\CostCalculator */
	public $costCalculator;

	public function __construct(CostCalculator $costCalculator) {
		$this->costCalculator = $costCalculator;
	}

	/**
	 * @inheritdoc
	 */
	public function predictAppliance(Appliance $app) {
	}

	/**
	 * @inheritdoc
	 */
	public function predictRunning() {
	}

	/**
	 * @inheritdoc
	 */
	public function predictAggregate(Carbon $startTime, Appliance $appliance, $withRunning = true) {
		$running = Run::with('loadCurve', 'loadCurve.loadData')
			->where('is_running', true)->get();
		$lastRun = Run::with('loadCurve', 'loadCurve.loadData')
			->where('appliance_id', $appliance->id)->orderBy('created_at', 'desc')->first();
		$expectedCurve = $this->reindexCurve($startTime, $lastRun->loadCurve);
		$curves = array();
		foreach ($running as $run) {
			$curves[] = $run->loadCurve;
		}

		$demands = $this->calculateDemands($startTime, $curves, $expectedCurve);
		$maxDemand = $demands[0];
		foreach ($demands as $demand) {
			if ($demand->watts > $maxDemand->watts) {
				$maxDemand = $demand;
			}
		}

		return $maxDemand;
	}

	/**
	 * @param \Carbon\Carbon $startTime
	 * @param \App\Model\LoadCurve $curve
	 * @return \App\Model\LoadCurve
	 */
	public function reindexCurve(Carbon $startTime, LoadCurve $curve) {
		$loadCurve = new LoadCurve();
		$time = $startTime->copy();
		foreach ($curve->loadData as $loadData) {
			/* @var \App\Model\LoadData $loadData */
			$newData = $loadData->copyLD();
			$newData->time = $time;
			$loadCurve->setDataAt($time, $newData, true);
			$time = $time->copy()->addMinute();
		}

		return $loadCurve;
	}

	/**
	 * @param Carbon $startTime
	 * @param $curves
	 * @return \App\Model\DemandHistory[]
	 */
	public function calculateDemands(Carbon $startTime, $curves, $expectedCurve) {
		$demandHistories = array();
		$demandHistory = new DemandHistory($this->costCalculator);
		$demandHistory->start($startTime);
		$demandHistories[] = $demandHistory;
		$curves[] = $expectedCurve;

		foreach ($curves as $curve) {
			/* @var \App\Model\LoadCurve $curve */
			foreach ($curve->loadData as $loadData) {
				/* @var \App\Model\LoadData $loadData */
				foreach ($demandHistories as $history) {
					/* @var \App\Model\DemandHistory $history */
					if ($loadData->time->lt($history->start_time)) {
						continue;
					} else if ($loadData->time->lte($history->end_time)) {
						$history->updateHistory($loadData);
					} else {
						continue;
						printf("Creating new demand history.\n");
						$newHistory = new DemandHistory($this->costCalculator);
						$newHistory->start($loadData->time);
						$newHistory->updateHistory($loadData);
						$demandHistories[] = $newHistory;
					}
				}
			}
		}

		return $demandHistories;
	}
}
