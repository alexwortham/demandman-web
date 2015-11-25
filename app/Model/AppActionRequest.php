<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\ActionRequest;

class AppActionRequest extends \Eloquent implements ActionRequest
{
	/**
	 * Mark the request as approved.
	 *
	 * @return void
	 */
	public function approve() {
		$this->status = "approved";
		$this->save();
	}

	/**
	 * Mark the request as denied.
	 *
	 * @return void
	 */
	public function deny() {
		$this->status = "denied";
		$this->save();
	}

	/**
	 * Get the request id.
	 *
	 * @return mixed The request id.
	 */
	public function requestId() {
		return $this->id;
	}

	/**
	 * Mark the request as completed successfully.
	 *
	 * @return void
	 */
	public function succeeded() {
	}

	/**
	 * Mark the request as incomplete/failed.
	 *
	 * @return void
	 */
	public function failed() {
	}

	/**
	 * Get the action.
	 *
	 * @return string The action.
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * Get the appliance id.
	 *
	 * @return int The appliance id.
	 */
	public function applianceId() {
		return $this->appId;
	}

	public function getStartTime() {
		return $this->started_at;
	}
}
