<?php

/**
 * Defines the message handling for the API.
 */
namespace App\Services;

use App\Model\Appliance;
use App\ActionRequest;
use App\Model\AppActionRequest;
use App\ActionResponse;
use App\Model\AppActionResponse;

/**
 * Defines the message handling for the API.
 */
interface ApiMessenger
{
	/**
	 * Broadcast an ActionRequest to subscribers.
	 *
	 * @param \App\ActionRequest $request The request to broadcast.
	 * @return void
	 */
	public function broadcastRequest(ActionRequest $request);

	/**
	 * Create an ActionRequest instance.
	 *
	 * @param \App\Model\Appliance $app The appliance under request.
	 * @param string $action The requested action.
	 * @return \App\ActionRequest A new ActionRequest instance.
	 */
	public function createRequest(Appliance $app, $action);

	/**
	 * Encode an ActionRequest as a string.
	 *
	 * @param \App\ActionRequest $request The ActionRequest to encode.
	 * @return string A string representation of the request.
	 */
	public function encodeRequest(ActionRequest $request);

	/**
	 * Decode a request string into an ActionRequest object.
	 *
	 * @param string $request The request string to decode.
	 * @return \App\ActionRequest The decoded ActionRequest object.
	 */
	public function decodeRequest($request);


	public function broadcastComplete(ActionRequest $request, AppActionResponse $response);

	/**
	 * Broadcast an ActionResponse to subscribers.
	 *
	 * @param \App\ActionResponse $response The response to broadcast.
	 * @return void
	 */
	public function broadcastResponse(ActionResponse $response);

	/**
	 * Create an ActionResponse instance.
	 *
	 * @param \App\ActionRequest $request The request to respond to.
	 * @param mixed $response The contents of the response.
	 */
	public function createResponse(ActionRequest $request, $response);

	/**
	 * Encode an ActionResponse as a string.
	 *
	 * @param \App\ActionResponse $response The ActionResponse to encode.
	 * @return string A string representation of the response.
	 */
	public function encodeResponse(ActionResponse $response);

	/**
	 * Decode a response string into an ActionResponse object.
	 *
	 * @param string $response The response string to decode.
	 * @return \App\ActionResponse The decoded ActionResponse object.
	 */
	public function decodeResponse($response);
}
