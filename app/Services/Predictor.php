<?php

/**
 * Predict the usage of an appliance.
 */
namespace App\Services;

use App\Appliance;

/**
 * Predict the usage of an appliance.
 */
interface Predictor
{
	/**
	 * Predict the usage of the given appliance.
	 *
	 * @param App\Appliance $app The Appliance to predict useage for.
	 * @return App\LoadCurve The predicted usage of this appliance.
	 */
	public function predictAppliance(Appliance $app);

	/**
	 * Predict the combined usage of all currently running appliances.
	 *
	 * @return App\LoadCurve The predicted usage of all running appliances.
	 */
	public function predictRunning();

	/**
	 * Predict the combined usage of the given appliances and optionally 
	 * include all currently running appliances (enabled by default).
	 *
	 * @param App\Appliances[] $appliances The appliances to predict.
	 * @param boolean $withRunning If true, include the predicted usage
	 * of all currently running appliances (enabled by default).
	 * @return App\LoadCurve The predicted aggregate usage of all inputs.
	 */
	public function predictAggregate($appliances, $withRunning = true);
}
