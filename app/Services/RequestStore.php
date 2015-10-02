<?php

/**
 * An interface for fetching ActionRequests from storage.
 */
namespace App\Services;

/**
 * An interface for fetching ActionRequests from storage.
 */
interface RequestStore
{
	/**
	 * Get an ActionRequest from storage.
	 *
	 * @param mixed $requestId The id of the request to get.
	 * @return App\ActionRequest The ActionRequest fetched from storage.
	 */
	public function get($requestId);
}
