<?php

/**
 * The API interface.
 */
namespace App\Services;

/**
 * The API interface.
 */
interface ApplianceApi
{
	/**
	 * Start an appliance.
	 *
	 * @param mixed $appId The id of an appliance.
	 * @return void
	 */
	public function startAppliance($appId);

	/**
	 * Stop an appliance.
	 *
	 * @param mixed $appId The id of an appliance.
	 * @return void
	 */
	public function stopAppliance($appId);

	/**
	 * Pause an appliance.
	 *
	 * @param mixed $appId The id of an appliance.
	 * @return void
	 */
	public function pauseAppliance($appId);

	/**
	 * Resume an appliance.
	 *
	 * @param mixed $appId The id of an appliance.
	 * @return void
	 */
	public function resumeAppliance($appId);

	/**
	 * Wake an appliance.
	 *
	 * @param mixed $appId The id of an appliance.
	 * @return void
	 */
	public function wakeAppliance($appId);

	/**
	 * Callback for appliances to confirm a successful start.
	 *
	 * @param mixed $requestId The id of the ActionRequest being confirmed.
	 * @param mixed $response The response from the caller.
	 * @return void
	 */
	public function confirmStart($requestId, $response);

	/**
	 * Callback for appliances to confirm a successful stop.
	 *
	 * @param mixed $requestId The id of the ActionRequest being confirmed.
	 * @param mixed $response The response from the caller.
	 * @return void
	 */
	public function confirmStop($requestId, $response);

	/**
	 * Callback for appliances to confirm a successful pause.
	 *
	 * @param mixed $requestId The id of the ActionRequest being confirmed.
	 * @param mixed $response The response from the caller.
	 * @return void
	 */
	public function confirmPause($requestId, $response);

	/**
	 * Callback for appliances to confirm a successful resume.
	 *
	 * @param mixed $requestId The id of the ActionRequest being confirmed.
	 * @param mixed $response The response from the caller.
	 * @return void
	 */
	public function confirmResume($requestId, $response);

	/**
	 * Callback for appliances to confirm a successful wake.
	 *
	 * @param mixed $requestId The id of the ActionRequest being confirmed.
	 * @param mixed $response The response from the caller.
	 * @return void
	 */
	public function confirmWake($requestId, $response);
}
