<?php

/**
 * A model class for storing a history of demand windows.
 *
 * Where a demand window is the averaging period for demand
 * charges.
 */
namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Services\CostCalculator;

/**
 * A model class for storing a history of demand windows.
 *
 * Where a demand window is the averaging period for demand
 * charges.
 *
 * @property \DateTime $start_time The time the period started.
 * @property \DateTime $end_time The time the period ended.
 * @property double $watts The average of the demand in watts for this period.
 * @property double $watt_hours The watt hours used in this period.
 * @property double $demand_charge The calculated demand charge for this period.
 * @property double $usage_charge The calculated usage charge for this period.
 */
class DemandHistory extends Model
{
	protected $calculator;
	public $sum;
	public $costPerKwHr;
	public $costPerKw;
	public $demandDeltaSecs;
	public $wattHrSum;

	public function __construct(CostCalculator $calculator) {
		$this->calculator = $calculator;
		$this->costPerKwHr = $calculator->costPerKiloWattHour();
		$this->costPerKw = $calculator->costPerKiloWatt();
		$this->demandDeltaSecs = $calculator->demandDeltaSeconds();
		$this->sum = 0;
		$this->wattHrSum = 0;
	}

	public function updateHistory($time, $watts) {
		if ($time - $this->start_time > $this->demandDeltaSecs) {
			return false;
		}
		$wattHours = ($watts / 3600.0);
		$this->wattHrSum += $wattHours;
		$this->usage_charge += ($wattHours / 1000.0) * $this->costPerKwHr;
		$this->sum += $watts;
		$this->demand_charge = 
			($this->sum / $this->demandDeltaSecs) * $this->costPerKw;

		return true;
	}

	public function start($now = time()) {
	}

	public function complete() {
	}

	public function billingCycle() {
		return $this->belongsTo('App\BillingCycle');
	}
}
