<?php

/**
 * An interface for a current monitoring device.
 */
namespace App;

/**
 * An interface for a current monitoring device.
 */
interface CurrentMonitor
{
	/**
	 * Get the value of the monitor in Amps.
	 *
	 * @param double $raw_value The raw value from the current sensor.
	 * @return double The value of the monitor in Amps.
	 */
	public function getAmps($raw_value);

	/**
	 * Get the value of the monitor in MilliAmps.
	 *
	 * @param double $raw_value The raw value from the current sensor.
	 * @return double The value of the monitor in MilliAmps.
	 */
	public function getMilliAmps($raw_value);

	/**
	 * Get the value of the monitor in Watts.
	 *
	 * @param double $raw_value The raw value from the current sensor.
	 * @return double The value of the monitor in Watts.
	 */
	public function getWatts($raw_value);

	/**
	 * Get the value of the monitor in KiloWatts.
	 *
	 * @param double $raw_value The raw value from the current sensor.
	 * @return double The value of the monitor in KiloWatts.
	 */
	public function getKiloWatts($raw_value);

	/**
	 * Get the voltage of the circuit to which this monitor is attached.
	 *
	 * @return int The voltage of the circuit to which this monitor is attached.
	 */
	public function getVoltage();

}
