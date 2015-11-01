<?php

/**
 * An interface for fetching ActionRequests from storage.
 */
namespace App\Services;

use App\Model\AppActionRequest;

/**
 * An interface for fetching ActionRequests from storage.
 */
class DatabaseRequestStore implements RequestStore
{
	/**
	 * Get an ActionRequest from storage.
	 *
	 * @param mixed $requestId The id of the request to get.
	 * @return \App\ActionRequest The ActionRequest fetched from storage.
	 */
	public function get($requestId) {
		return AppActionRequest::find($requestId);
	}
}
