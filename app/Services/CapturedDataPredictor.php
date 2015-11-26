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
		$running = Run::with(['loadCurve.loadData' => function($query) {
				$query->orderBy('idx', 'desc')->take(1);
			}])->where('is_running', true)->get();
		$lastRun = Run::with('loadCurve', 'loadCurve.loadData')
			->where('appliance_id', $appliance->id)->orderBy('created_at', 'desc')->first();
		if ($lastRun === NULL) {
			//return 0;
		}
		$expectedCurve = $this->reindexCurve($startTime, $lastRun->loadCurve);
		$curves = array();
		foreach ($running as $run) {
			$lastRun = Run::with('loadCurve', 'loadCurve.loadData')
				->where('appliance_id', $appliance->id)
				->where('is_running', false)
				->orderBy('created_at', 'desc')->first();
			$lastData = $run->loadCurve->loadData->first();
			$elapsed = 0;
			if ($lastData !== NULL) {
				$elapsed = $lastData->idx;
			}
			$reindexed = $this->reindexCurve($startTime, $lastRun->loadCurve, $elapsed);
			$curves[] = $reindexed;
		}
		$curves[] = $expectedCurve;

		$demands = array_values($this->calculateDemands($startTime, [], $curves));
		$maxDemand = $demands[0];
		foreach ($demands as $demand) {
			if ($demand->watts > $maxDemand->watts) {
				$maxDemand = $demand;
			}
		}

		return $maxDemand->watts;
	}

	/**
	 * @param \Carbon\Carbon $startTime
	 * @param \App\Model\LoadCurve $curve
	 * @return \App\Model\LoadCurve
	 */
	public function reindexCurve(Carbon $startTime, LoadCurve $curve, $elapsed = 0) {
		$loadCurve = new LoadCurve();
		$time = $startTime->copy();
		$time->modify((int) $elapsed.' minutes ago');
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
	public function calculateDemands(Carbon $startTime, $curves, $expectedCurves) {
		$demandHistories = array();
		$demandHistory = new DemandHistory($this->costCalculator);
		$demandHistory->start($startTime);
		/* @var \App\Model\DemandHistory[] $demandHistories */
		$demandHistories[$demandHistory->start_time->toDateTimeString()] = $demandHistory;
		$zaCurves = array();
		foreach ($curves as $curve) {
			/* @var \App\Model\LoadCurve $curve */
			$curve->load_data = $curve->loadData()->get()->all();
			$zaCurves[] = $curve;
		}
		foreach ($expectedCurves as $expectedCurve) {
			$zaCurves[] = $expectedCurve;
		}

		foreach ($zaCurves as $curve) {
			$lasto = 0;
			/* @var \App\Model\LoadCurve $curve */
			foreach (array_slice($curve->load_data, $lasto, NULL, true)
					 as $loadData) {
				/* @var \App\Model\LoadData $loadData */
                if ($loadData->time->gte($demandHistory->start_time) &&
                        $loadData->time->lte($demandHistory->end_time)) {
                    $demandHistory->updateHistory($loadData);
                //} else if ($loadData->time->gt($demandHistory->end_time)) {
				} else {
                    $newHistory = new DemandHistory($this->costCalculator);
                    $newHistory->start($loadData->time);
					$newKey = $newHistory->start_time->toDateTimeString();
					$oldKey = $demandHistory->start_time->toDateTimeString();
					if (array_key_exists($newKey, $demandHistories)) {
						$newHistory = $demandHistories[$newKey];
						$newHistory->updateHistory($loadData);
						$demandHistory = $newHistory;
					} else {
						$demandHistories[$oldKey] = $demandHistory;
						$demandHistories[$newKey] = $newHistory;
						$demandHistory = $newHistory;
					}
                }
				$lasto++;
			}
		}

		return $demandHistories;
	}
}
