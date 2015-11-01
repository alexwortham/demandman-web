<?php

namespace App\Model;

use App\ActionResponse;

class AppActionResponse extends \Eloquent implements ActionResponse
{
	/**
	 * @inheritdoc
	 */
	public function approved() {
	}

	/**
	 * @inheritdoc
	 */
	public function denied() {
	}

	/**
	 * @inheritdoc
	 */
	public function requestId() {
		return $this->id;
	}

	/**
	 * @inheritdoc
	 */
	public function succeeded() {
	}

	/**
	 * @inheritdoc
	 */
	public function failed() {
	}

	/**
	 * @inheritdoc
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * @inheritdoc
	 */
	public function applianceId() {
		return $this->appId;
	}

}
