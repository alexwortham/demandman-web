<?php

/**
 * Model class for storing information on electricity rates.
 */
namespace App\Model;

/**
 * Model class for storing information on electricity rates.
 *
 * @property double $usage_rate The charge per kW/hr.
 * @property double $demand_rate The charge per kW.
 * @property int $day_of_month The day of month the bill is due.
 * @property int $demand_delta The averaging period for demand charges in minutes.
 * @property boolean $is_current True if this is the current rate used in calculations.
 */
class ElectricityRate extends \Eloquent
{
    //
	public function billingCycles() {
		return $this->hasMany('App\Model\BillingCycle');
	}
}
