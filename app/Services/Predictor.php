<?php

/**
 * Predict the usage of an appliance.
 */
namespace App\Services;

use App\Model\Appliance;
use \Carbon\Carbon;

/**
 * Predict the usage of an appliance.
 */
interface Predictor
{
	/**
	 * Predict the usage of the given appliance.
	 *
	 * @param \App\Model\Appliance $app The Appliance to predict useage for.
	 * @return \App\Model\LoadCurve The predicted usage of this appliance.
	 */
	public function predictAppliance(Appliance $app);

	/**
	 * Predict the combined usage of all currently running appliances.
	 *
	 * @return \App\Model\LoadCurve The predicted usage of all running appliances.
	 */
	public function predictRunning();

	/**
	 * Predict the combined usage of the given appliances and optionally 
	 * include all currently running appliances (enabled by default).
	 *
	 * @param \Carbon\Carbon $startTime The time at which the appliance would start.
	 * @param \App\Model\Appliance $appliance The appliance to predict.
	 * @param boolean $withRunning If true, include the predicted usage
	 * of all currently running appliances (enabled by default).
	 * @return \App\Model\DemandHistory The highest predicted DemandHistory.
	 */
	public function predictAggregate(Carbon $startTime, Appliance $appliance, $withRunning = true);
}
