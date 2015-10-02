<?php

namespace App\Services;

/**
 * Predict the usage of an appliance.
 */
interface Predictor
{
	public function predict($appId);
}
