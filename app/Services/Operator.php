<?php

namespace App\Services;

/**
 * Determins when appliances should be run... And runs them if necessary.
 */
interface Operator
{
	/* Callbacks when appliance actions occur */

	public function applianceStarted($appId);

	public function applianceStarting($appId);

	public function applianceStopped($appId);

	public function applianceStopping($appId);

	public function appliancePaused($appId);

	public function appliancePausing($appId);

	public function applianceResumed($appId);

	public function applianceResuming($appId);

	public function applianceWaking($appId);

	public function applianceWaked($appId);
}
