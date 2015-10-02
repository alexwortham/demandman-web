<?php

/**
 * Main API Control Class.
 *
 * This class implements the ApplianceApi interface and contains the control
 * logic for the main API. Its extensive use of injected interfaces should 
 * make it easy to swap code in and out and make it easy to test.
 */
namespace App\Services;
use App\ActionRequest;
use App\Appliance;

/**
 * Main API Control Class.
 *
 * This class implements the ApplianceApi interface and contains the control
 * logic for the main API. Its extensive use of injected interfaces should 
 * make it easy to swap code in and out and make it easy to test.
 */
class ControllerService implements ApplianceApi
{
	protected $manager;
	protected $messenger;
	protected $applianceStore;
	protected $requestStore;

	public function __construct(Manager $manager, 
			ApiMessenger $messenger,
			ApplianceStore $applianceStore,
			RequestStore $requestStore) {
		$this->manager = $manager;
		$this->messenger = $messenger;
		$this->applianceStore = $applianceStore;
		$this->requestStore = $requestStore;
	}

	/**
	 * Execute an API action.
	 *
	 * Gets the appliance from the data store, creates an action request,
	 * calls the desired method on the manager for approval, and broadcasts the result.
	 *
	 * @param mixed $appId The appliance to act on.
	 * @param string $action The action to execute.
	 * @return void
	 */
	private function doAction($appId, $action) {
		$appliance = $this->applianceStore->get($appId);
		
		$actionRequest = $this->messenger->createRequest($appliance, $action);

		call_user_func_array(array($this, "do$action"), array($appliance, $actionRequest));

		$this->messenger->broadcastRequest($request);
	}

	/**
	 * Execute request confirmation.
	 *
	 * Gets the request from the request store, constructs a response object,
	 * informs the manager of confirmation, and broadcasts result.
	 *
	 * @param mixed $requestId The request id.
	 * @param mixed $response The response data.
	 * @param string $action The action to execute.
	 * @return void
	 */
	private function doConfirm($requestId, $response, $action) {
		$request = $this->requestStore->get($requestId);
		$response = $this->messenger->createResponse($request, $response);

		call_user_func_array(array($this->manager, "confirm$action"), array($request, $response));

		$this->messenger->broadcastResponse($response);
	}

	/**
	 * Execute the start request.
	 *
	 * @param App\Appliance $appliance The appliance to act on.
	 * @param App\ActionRequest $request The request to act on.
	 * @return void
	 */
	private function doStart(Appliance $appliance, ActionRequest $request) {
		if ($this->manager->startAppliance($request)) {
			$actionRequest->approve();
		} else {
			$actionRequest->deny();
		}
	}

	/**
	 * Execute the stop request.
	 *
	 * @param App\Appliance $appliance The appliance to act on.
	 * @param App\ActionRequest $request The request to act on.
	 * @return void
	 */
	private function doStop(Appliance $appliance, ActionRequest $request) {
		if ($this->manager->stopAppliance($request)) {
			$actionRequest->approve();
		} else {
			$actionRequest->deny();
		}
	}

	/**
	 * Execute the pause request.
	 *
	 * @param App\Appliance $appliance The appliance to act on.
	 * @param App\ActionRequest $request The request to act on.
	 * @return void
	 */
	private function doPause(Appliance $appliance, ActionRequest $request) {
		if ($this->manager->pauseAppliance($request)) {
			$actionRequest->approve();
		} else {
			$actionRequest->deny();
		}
	}

	/**
	 * Execute the resume request.
	 *
	 * @param App\Appliance $appliance The appliance to act on.
	 * @param App\ActionRequest $request The request to act on.
	 * @return void
	 */
	private function doResume(Appliance $appliance, ActionRequest $request) {
		if ($this->manager->resumeAppliance($request)) {
			$actionRequest->approve();
		} else {
			$actionRequest->deny();
		}
	}

	/**
	 * Execute the wake request.
	 *
	 * @param App\Appliance $appliance The appliance to act on.
	 * @param App\ActionRequest $request The request to act on.
	 * @return void
	 */
	private function doWake(Appliance $appliance, ActionRequest $request) {
		if ($this->manager->wakeAppliance($request)) {
			$actionRequest->approve();
		} else {
			$actionRequest->deny();
		}
	}

	/**
	 * @inheritdoc
	 */
	public function startAppliance($appId) {
		$this->doAction($appId, 'Start');
	}

	/**
	 * @inheritdoc
	 */
	public function stopAppliance($appId) {
		$this->doAction($appId, 'Stop');
	}

	/**
	 * @inheritdoc
	 */
	public function pauseAppliance($appId) {
		$this->doAction($appId, 'Pause');
	}

	/**
	 * @inheritdoc
	 */
	public function resumeAppliance($appId) {
		$this->doAction($appId, 'Resume');
	}

	/**
	 * @inheritdoc
	 */
	public function wakeAppliance($appId) {
		$this->doAction($appId, 'Wake');
	}

	/**
	 * @inheritdoc
	 */
	public function confirmStart($requestId, $response) {
		$this->doConfirm($requestId, $reponse, 'Start');
	}

	/**
	 * @inheritdoc
	 */
	public function confirmStop($requestId, $response) {
		$this->doConfirm($requestId, $reponse, 'Stop');
	}

	/**
	 * @inheritdoc
	 */
	public function confirmPause($requestId, $response) {
		$this->doConfirm($requestId, $reponse, 'Pause');
	}

	/**
	 * @inheritdoc
	 */
	public function confirmResume($requestId, $response) {
		$this->doConfirm($requestId, $reponse, 'Resume');
	}

	/**
	 * @inheritdoc
	 */
	public function confirmWake($requestId, $response) {
		$this->doConfirm($requestId, $reponse, 'Wake');
	}
}
