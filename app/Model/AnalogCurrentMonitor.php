<?php

/**
 * A model class for current sensors.
 */
namespace App\Model;

use App\CurrentMonitor;

/**
 * A model class for current sensors.
 *
 * @property int $ain_number The AIN number to monitor. (E.g. AIN0)
 * @property \App\Model\Appliance $appliance The appliance to which
 * this monitor is attached.
 * @property string $name An optional name for the monitor.
 * @property int $voltage The Voltage of the circuit to which this
 * monitor is attached.
 * @property double $sensitivity The sensitivity of the Analog sensor
 * in mV/A.
 * @property double $bias The zero point of the sensor in milliVolts.
 * @property \App\Model\LoadData[] The LoadDatas associated with this
 * AnalogCurrentMonitor.
 */
class AnalogCurrentMonitor extends \Eloquent implements CurrentMonitor
{
	/**
	 * @inheritdoc
	 */
	public function getAmps($raw_value) {
		$val = abs($raw_value - $this->bias);
		if ($val < 5) { //5mV tolerance for 0 point.
			return 0;
		} else {
			return $val * $this->sensitivity;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function getMilliAmps($raw_value) {
		return $this->getAmps($raw_value) * 1000.0;
	}

	/**
	 * @inheritdoc
	 */
	public function getWatts($raw_value) {
		return $this->getAmps($raw_value) * $this->getVoltage();
	}

	/**
	 * @inheritdoc
	 */
	public function getKiloWatts($raw_value) {
		return $this->getWatts($raw_value) / 1000.0;
	}

	/**
	 * @inheritdoc
	 */
	public function getVoltage() {
		return $this->voltage;
	}

	/**
	 * Get the Appliance associated with this CurrentMonitor.
	 *
	 * @return \App\Model\Appliance The Appliance associated with this CurrentMonitor.
	 */
	public function appliance() {
		return $this->belongsTo('App\Model\Appliance');
	}

	/**
	 * Get the LoadDatas associated with this AnalogCurrentMonitor.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function loadData() {
		return $this->hasMany('App\Model\LoadData');
	}

}
