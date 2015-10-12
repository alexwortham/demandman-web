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
	 * Perform any actions necessary to setup the current monitor.
	 *
	 * @return void
	 */
	public function setup();
}
