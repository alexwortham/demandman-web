<?php

/**
 * Decides if appliances are allowed to run.
 *
 * Decides based on demand.
 */
namespace App\Services;

use App\ActionRequest;
use App\ActionResponse;

/**
 * Decides if appliances are allowed to run.
 *
 * Decides based on demand.
 */
class DemandManager implements Manager
{
	public function startAppliance(ActionRequest $request) {
	}

	public function stopAppliance(ActionRequest $request) {
	}

	public function pauseAppliance(ActionRequest $request) {
	}

	public function resumeAppliance(ActionRequest $request) {
	}

	public function wakeAppliance(ActionRequest $request) {
	}

	public function confirmStart(ActionRequest $request, ActionResponse $response) {
	}

	public function confirmStop(ActionRequest $request, ActionResponse $response) {
	}

	public function confirmPause(ActionRequest $request, ActionResponse $response) {
	}

	public function confirmResume(ActionRequest $request, ActionResponse $response) {
	}

	public function confirmWake(ActionRequest $request, ActionResponse $response) {
	}
}
