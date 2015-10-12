<?php

/**
 * A model class for current sensors.
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * A model class for current sensors.
 *
 * @property int $ain_number The AIN number to monitor. (E.g. AIN0)
 * @property App\Appliance $appliance The appliance to which this monitor is attached.
 * @property string $name An optional name for the monitor.
 */
class AnalogCurrentMonitor extends Model implements CurrentMonitor
{

	/**
	 * @inheritdoc
	 */
	public function getAmps() {

	}

	/**
	 * @inheritdoc
	 */
	public function getMilliAmps() {

	}

	/**
	 * @inheritdoc
	 */
	public function setup() {

	}

	/**
	 * Get the Appliance associated with this CurrentMonitor.
	 *
	 * @return App\Appliance The Appliance associated with this CurrentMonitor.
	 */
	public function appliance() {
		return $this->belongsTo('App\Appliance');
	}
}
