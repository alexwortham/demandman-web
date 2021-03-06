<?php

/**
 * An interface for a current metering service.
 */
namespace App\Services;

/**
 * An interface for a current metering service.
 */
interface Meter
{
	/**
	 * Start metering for the given appliance.
	 *
	 * @param int $appId The Appliance to start metering.
	 * @return boolean True if started successfully, false otherwise.
	 */
	public function appStart($appId);

	/**
	 * Stop metering for the given appliance.
	 *
	 * @param int $appId The Appliance to stop metering.
	 * @return boolean True if stopped successfully, false otherwise.
	 */
	public function appStop($appId);

	/**
	 * Set an active event on the meter.
	 *
	 * @param mixed $event
	 * @return void
	 */
	public function setEvent($event);

	/**
	 * Execute the loop to take and record measurements from the
	 * analog sensors.
	 *
	 * @return void
	 */
	public function meterLoop();
}
