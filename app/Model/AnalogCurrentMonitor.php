<?php

/**
 * A model class for current sensors.
 */
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\CurrentMonitor;
use App\Analog;

/**
 * A model class for current sensors.
 *
 * @property int $ain_number The AIN number to monitor. (E.g. AIN0)
 * @property App\Appliance $appliance The appliance to which this monitor is attached.
 * @property string $name An optional name for the monitor.
 * @property int $voltage The Voltage of the circuit to which this monitor is attached.
 * @property double $sensitivity The sensitivity of the Analog sensor in mV/A.
 * @property double $bias The zero point of the sensor in milliVolts.
 */
class AnalogCurrentMonitor extends Model implements CurrentMonitor
{
	/** @var App\Analog $analog The analog input for the monitor */
	private $analog;

	/**
	 * @inheritdoc
	 */
	public function getAmps() {
		return abs($this->analog->read_raw() - $this->bias) * $this->sensitivity;
	}

	/**
	 * @inheritdoc
	 */
	public function getMilliAmps() {
		return $this->getAmps() * 1000.0;
	}

	/**
	 * @inheritdoc
	 */
	public function getWatts() {
		return $this->getAmps() * $this->getVoltage();
	}

	/**
	 * @inheritdoc
	 */
	public function getKiloWatts() {
		return $this->getWatts() / 1000.0;
	}

	/**
	 * @inheritdoc
	 */
	public function getVoltage() {
		return $this->voltage;
	}

	/**
	 * @inheritdoc
	 */
	public function setup() {
		$this->analog = new Analog($this->ain_number);
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
