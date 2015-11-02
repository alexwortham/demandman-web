<?php

/**
 * An interface for calculating electricity costs.
 */
namespace App\Services;

use App\Model\LoadCurve;

/**
 * An interface for calculating electricity costs.
 */
interface CostCalculator
{
	/**
	 * Calculate the demand charge of the given LoadCurve. (maxima)
	 *
	 * @param \App\Model\LoadCurve $curve The LoadCurve to calculate charges for.
	 * @return double The calculated demand charge.
	 */
	public function demandCost(LoadCurve $curve);

	/**
	 * Calculate the usage charge of the given LoadCurve. (integral)
	 *
	 * @param \App\Model\LoadCurve $curve The LoadCurve to calculate charges for.
	 * @return double The calculated demand charge.
	 */
	public function usageCost(LoadCurve $curve);

	/**
	 * Return the demand cost for the current billing cycle.
	 *
	 * @return double The demand cost for the current billing cycle.
	 */
	public function currentBillDemandCost();

	/**
	 * Return the usage cost for the current billing cycle.
	 *
	 * @return double The usage cost for the current billing cycle.
	 */
	public function currentBillUsageCost();

	/**
	 * Return both the demand and usage costs for the current billing cycle.
	 *
	 * @return double The demand and usage costs for the current billing cycle.
	 */
	public function currentBillTotalCost();

	public function costPerKiloWatt();

	public function costPerKiloWattHour();

	public function demandDeltaSeconds();

	public function demandDeltaMinutes();

	/**
	 * Get the current BillingCycle.
	 *
	 * @return \App\Model\BillingCycle
	 */
	public function getCurrentBillingCycle();
}
