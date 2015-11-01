<?php

/**
 * A model class for storing information on billing cycles.
 */
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * A model class for storing information on billing cycles.
 *
 * @property \DateTime $begin_date The date the cycle began.
 * @property \DateTime $end_date The date the cycle ended (nullable).
 * @property \App\Model\ElectricityRate $rate The ElectricityRate used for this billing
 * cycle.
 * @property \App\Model\DemandHistory $histories The DemandHistory's associated with this
 * billing cycle.
 * @property boolean $is_current True if this is the current billing cycle.
 * @property double $demand_charge A cached calculation of the demand charge.
 * @property double $usage_charge A cached calculation of the usage charge.
 * @property \DateTime $charges_updated The date of the last time that the
 * cached charge values (`$demand_charge` and `$usage_charge`) were updated.
 */
class BillingCycle extends Model
{
    //
	public function histories() {
		return $this->hasMany('App\Model\DemandHistory');
	}

	public function electricityRate() {
		return $this->belongsTo('App\Model\ElectricityRate');
	}
}
