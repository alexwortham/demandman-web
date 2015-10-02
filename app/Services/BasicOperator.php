<?php

namespace App\Services;

class BasicOperator implements Operator
{
	/* Callbacks when appliance actions occur */

	public function applianceStarted($appId) {
	}

	public function applianceStarting($appId) {
	}

	public function applianceStopped($appId) {
	}

	public function applianceStopping($appId) {
	}

	public function appliancePaused($appId) {
	}

	public function appliancePausing($appId) {
	}

	public function applianceResumed($appId) {
	}

	public function applianceResuming($appId) {
	}

	public function applianceWaking($appId) {
	}

	public function applianceWaked($appId) {
	}

	/* Actions taken to start an appliance */

	public function startAppliance($appId) {
	}

	public function stopAppliance($appId) {
	}

	public function pauseAppliance($appId) {
	}

	public function resumeAppliance($appId) {
	}

}
