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
	 * @return double The value of the monitor in Amps.
	 */
	public function getAmps();

	/**
	 * Get the value of the monitor in MilliAmps.
	 *
	 * @return double The value of the monitor in MilliAmps.
	 */
	public function getMilliAmps();

	/**
	 * Get the value of the monitor in Watts.
	 * 
	 * @return double The value of the monitor in Watts.
	 */
	public function getWatts();

	/**
	 * Get the value of the monitor in KiloWatts.
	 * 
	 * @return double The value of the monitor in KiloWatts.
	 */
	public function getKiloWatts();

	/**
	 * Get the voltage of the circuit to which this monitor is attached.
	 *
	 * @return int The voltage of the circuit to which this monitor is attached.
	 */
	public function getVoltage();

	/**
	 * Perform any actions necessary to setup the current monitor.
	 *
	 * @return void
	 */
	public function setup();
}
