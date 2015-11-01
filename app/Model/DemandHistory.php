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
	protected $calculator;
	public $sum;
	public $costPerKwHr;
	public $costPerKw;
	public $demandDeltaSecs;
	public $demandDeltaMins;
	public $wattHrSum;

	/**
	 * Create a DemandHistory object using the given CostCalculator.
	 *
	 * @param \App\Services\CostCalculator $calculator The calculator object.
	 */
	public function __construct(CostCalculator $calculator) {
		parent::__construct();
		$this->calculator = $calculator;
		$this->costPerKwHr = $calculator->costPerKiloWattHour();
		$this->costPerKw = $calculator->costPerKiloWatt();
		$this->demandDeltaSecs = $calculator->demandDeltaSeconds();
		$this->demandDeltaMins = $calculator->demandDeltaMinutes();
		$this->sum = 0;
		$this->wattHrSum = 0;
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
		$wattHours = ($data->load / 3600.0);
		$this->wattHrSum += $wattHours;
		$this->usage_charge += ($wattHours / 1000.0) * $this->costPerKwHr;
		$this->sum += $data->load;
		$this->demand_charge = 
			($this->sum / $this->demandDeltaSecs) * $this->costPerKw;

		return true;
	}

	/**
	 * Set `$start_time` by rounding to the nearest `$this->demandDeltaMins`.
	 *
	 * @param bool|true $now Find `$start_time` based on the time now.
	 * @return void
	 */
	public function start($now = true) {
		if ($now === true) {
			$this->start_time = Carbon::now()->second(0);
			$this->start_time->minute(
				intval($this->start_time->minute / $this->demandDeltaMins)
				* $this->demandDeltaMins );
		}
	}

	/**
	 * Set `$end_time` by adding `$this->demandDeltaMins` to `$start_time`.
	 *
	 * @return void
	 */
	public function complete() {
		$this->end_time = $this->start_time->copy()
			->addMinutes($this->demandDeltaMins)
			->subSecond();
	}

	/**
	 * Get the BillingCycle associated with this DemandHistory.
	 *
	 * @return \App\Model\BillingCycle
	 */
	public function billingCycle() {
		return $this->belongsTo('App\Model\BillingCycle');
	}
}
