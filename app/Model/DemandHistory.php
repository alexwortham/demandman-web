<?php

/**
 * A model class for storing a history of demand windows.
 *
 * Where a demand window is the averaging period for demand
 * charges.
 */
namespace App\Model;

use App\Services\CostCalculator;
use Carbon\Carbon;

/**
 * A model class for storing a history of demand windows.
 *
 * Where a demand window is the averaging period for demand
 * charges.
 *
 * @property \Carbon\Carbon $start_time The time the period started.
 * @property \Carbon\Carbon $end_time The time the period ended.
 * @property double $watts The average of the demand in watts for this period.
 * @property double $watt_hours The watt hours used in this period.
 * @property double $demand_charge The calculated demand charge for this period.
 * @property double $usage_charge The calculated usage charge for this period.
 */
class DemandHistory extends \Eloquent
{
	/** @var CostCalculator $calculator CostCalculator used for calculations. */
	protected $calculator;
	/** @var int $sum Running sum used for tracking kW usage. */
	public $sum;
	/** @var double $costPerKwHr Cost per kwHr. */
	public $costPerKwHr;
	/** @var double $costPerKw Cost per Kw. */
	public $costPerKw;
	/** @var int $demandDeltaSecs The width of the demand averaging window in secs. */
	public $demandDeltaSecs;
	/** @var int $demandDeltaMins The width of the demand averaging window in minutes. */
	public $demandDeltaMins;
	/** @var int $wattHrSum Riemann sum used for tracking kWHr usage. */
	public $wattHrSum;
	/** @var BillingCycle $billCycle The current BillingCycle. */
	public $billCycle;

	/**
	 * Create a DemandHistory object using the given CostCalculator.
	 *
	 * @param \App\Services\CostCalculator $calculator The calculator object.
	 */
	public static function construct(CostCalculator $calculator) {
		$demand = new DemandHistory($calculator);
		$demand->calculator = $calculator;
		$demand->costPerKwHr = $calculator->costPerKiloWattHour();
		$demand->costPerKw = $calculator->costPerKiloWatt();
		$demand->demandDeltaSecs = $calculator->demandDeltaSeconds();
		$demand->demandDeltaMins = $calculator->demandDeltaMinutes();
		$demand->billCycle = $calculator->getCurrentBillingCycle();
		$demand->sum = 0;
		$demand->wattHrSum = 0;

		return $demand;
	}

	/**
	 * Update this DemandHistory with the given values.
	 *
	 * The function returns false if a value is attempted to be set outside
	 * the `$this->demandDeltaSecs` interval.
	 *
	 * @param \App\Model\LoadData $data The LoadData to update with.
	 * @return bool True if successful, false otherwise.
	 */
	public function updateHistory($data) {

		if ($data->time->timestamp - $this->start_time->timestamp > $this->demandDeltaSecs) {
			return false;
		}
		$wattHours = ($data->load / 60.0);
		$this->watt_hours += $wattHours;
		$this->usage_charge += ($wattHours / 1000.0) * $this->costPerKwHr;
		$this->sum += $data->load;
		$this->watts = ($this->sum / $this->demandDeltaMins);
		$this->demand_charge =
			($this->sum / $this->demandDeltaMins) * ($this->costPerKw / 1000.0);
		return true;
	}

	/**
	 * Update the demand history with the given curve.
	 *
	 * The function returns false if a value is attempted to be set outside
	 * the `$this->demandDeltaSecs` interval.
	 *
	 * @param Carbon $start
	 * @param Carbon $end
	 * @param LoadCurve $curve
	 * @return boolean True if successful, false otherwise.
	 */
	public function updateHistoryWithCurve(Carbon $start,
										   Carbon $end,
										   LoadCurve $curve) {

		for ($it = $start->copy(); $it->timestamp <= $end->timestamp; $it->addSecond()) {
			$data = $curve->getDataAt($it->timestamp);
			if ($data !== NULL) {
				$ret = $this->updateHistory($data);
				if ($ret === false) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Set `$start_time` by rounding to the nearest `$this->demandDeltaMins`.
	 *
	 * @param \Carbon\Carbon $time Find `$start_time` based on the time now.
	 * @return void
	 */
	public function start($time) {
        $this->start_time = $time->copy()->second(0);
        $this->start_time->minute(
            intval($this->start_time->minute / $this->demandDeltaMins)
            * $this->demandDeltaMins );
		$this->end_time = $this->start_time->copy()
			->addMinutes($this->demandDeltaMins)
			->subSecond();
	}

	/**
	 * Set `$end_time` by adding `$this->demandDeltaMins` to `$start_time`.
	 *
	 * @return void
	 */
	public function complete() {
		$this->billingCycle()->associate($this->billCycle);
	}

	/**
	 * Get the BillingCycle associated with this DemandHistory.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function billingCycle() {
		return $this->belongsTo('App\Model\BillingCycle');
	}
}
