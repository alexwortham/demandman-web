<?php

/**
 * Decides if appliances are allowed to run and coordinates management tasks
 * such as opening and closing relays, etc.
 */
namespace App\Services;

use App\ActionRequest;
use App\ActionResponse;

/**
 * Decides if appliances are allowed to run and coordinates management tasks
 * such as opening and closing relays, etc.
 */
interface Manager {
	
	/**
	 * Approve or deny a request to start an appliance.
	 *
	 * @param App\ActionRequest $request The ActionRequest to approve/deny.
	 * @return boolean True if approved, false otherwise.
	 */
	public function startAppliance(ActionRequest $request);

	/**
	 * Approve or deny a request to stop an appliance.
	 *
	 * @param App\ActionRequest $request The ActionRequest to approve/deny.
	 * @return boolean True if approved, false otherwise.
	 */
	public function stopAppliance(ActionRequest $request);

	/**
	 * Approve or deny a request to pause an appliance.
	 *
	 * @param App\ActionRequest $request The ActionRequest to approve/deny.
	 * @return boolean True if approved, false otherwise.
	 */
	public function pauseAppliance(ActionRequest $request);

	/**
	 * Approve or deny a request to resume an appliance.
	 *
	 * @param App\ActionRequest $request The ActionRequest to approve/deny.
	 * @return boolean True if approved, false otherwise.
	 */
	public function resumeAppliance(ActionRequest $request);

	/**
	 * Approve or deny a request to wake an appliance.
	 *
	 * @param App\ActionRequest $request The ActionRequest to approve/deny.
	 * @return boolean True if approved, false otherwise.
	 */
	public function wakeAppliance(ActionRequest $request);

	public function confirmStart(ActionRequest $request, ActionResponse $response);

	public function confirmStop(ActionRequest $request, ActionResponse $response);

	public function confirmPause(ActionRequest $request, ActionResponse $response);

	public function confirmResume(ActionRequest $request, ActionResponse $response);

	public function confirmWake(ActionRequest $request, ActionResponse $response);
}
