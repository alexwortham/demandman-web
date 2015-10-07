<?php

/**
 * An interface for handling action response data.
 */
namespace App;

/**
 * An interface for handling action response data.
 */
interface ActionResponse
{
	/**
	 * Check if the response indicates an approved request.
	 *
	 * @return boolean True if approved, false otherwise.
	 */
	public function approved();

	/**
	 * Check if the response indicates a denied request.
	 *
	 * @return boolean True if denied, false otherwise.
	 */
	public function denied();

	/**
	 * Get the request id.
	 *
	 * @return mixed The request id.
	 */
	public function requestId();

	/**
	 * Check if the response indicates a successful request.
	 *
	 * @return boolean True if succeeded, false otherwise.
	 */
	public function succeeded();

	/**
	 * Check if the response indicates a failed request.
	 *
	 * @return boolean True if failed, false otherwise.
	 */
	public function failed();

	/**
	 * Get the action.
	 *
	 * @return string The action.
	 */
	public function getAction();

	/**
	 * Get the appliance id.
	 *
	 * @return int The appliance id.
	 */
	public function applianceId();

}
