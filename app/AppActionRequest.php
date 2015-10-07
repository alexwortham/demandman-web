<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppActionRequest extends Model implements ActionRequest
{
	/**
	 * Mark the request as approved.
	 *
	 * @return void
	 */
	public function approve() {
	}

	/**
	 * Mark the request as denied.
	 *
	 * @return void
	 */
	public function deny() {
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
}
