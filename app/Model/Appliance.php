<?php

/**
 * Database Model class for Appliances.
 */
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Database Model class for Appliances.
 *
 * @property string $type Enum: type of appliance. Valid values: 'hvac',
 * 'wheater', 'dishwash', 'dryer'.
 * @property string $name A name for this appliance.
 */
class Appliance extends Model {

	/**
	 * @var string $table The name of the appliances table: 'appliances'.
	 */
	protected $table = 'appliances';
	/**
	 * @var boolean $timestamps Use timestamps.
	 */
	public $timestamps = true;

	/**
	 * Get the simulations associated with this appliance.
	 *
	 * @return \App\Model\Simulation[] An array of Simulations
	 * associated with this appliance.
	 */
	public function simulations() {
		return $this->hasMany('App\Model\Simulation');
	}

	/**
	 * The the runs associated with this appliance.
	 *
	 * @return \App\Model\Run[] An array of Runs associated with this appliance.
	 */
	public function runs() {
		return $this->hasMany('App\Model\Run');
	}

	public function loadData() {
		return $this->hasMany('App\Model\LoadData');
	}

	/**
	 * Get the AnalogCurrentMonitor associated with this appliance.
	 *
	 * @return \App\Model\AnalogCurrentMonitor The AnalogCurrentMonitor
	 * associated with this appliance.
	 */
	public function currentMonitor() {
		return $this->hasOne('App\Model\AnalogCurrentMonitor');
	}
}
