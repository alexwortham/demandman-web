<?php

/**
 * An implementation of the Predictor interface.
 *
 * Uses captured data to predict future usage.
 */
namespace App\Services;

use App\Appliance;

/**
 * An implementation of the Predictor interface.
 *
 * Uses captured data to predict future usage.
 */
class CapturedDataPredictor implements Predictor
{
	/**
	 * @inheritdoc
	 */
	public function predictAppliance(Appliance $app) {
	}

	/**
	 * @inheritdoc
	 */
	public function predictRunning() {
	}

	/**
	 * @inheritdoc
	 */
	public function predictAggregate($appliances, $withRunning = true) {
	}
}
