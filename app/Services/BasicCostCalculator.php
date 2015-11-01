<?php

/**
 * An implementation of the CostCalculator interface.
 */
namespace App\Services;

use App\Model\LoadCurve;

/**
 * An implementation of the CostCalculator interface.
 */
class BasicCostCalculator implements CostCalculator
{
	/**
	 * @inheritdoc
	 */
	public function demandCost(LoadCurve $curve) {
	}

	/**
	 * @inheritdoc
	 */
	public function usageCost(LoadCurve $curve) {
	}

	/**
	 * @inheritdoc
	 */
	public function currentBillDemandCost() {
	}

	/**
	 * @inheritdoc
	 */
	public function currentBillUsageCost() {
	}

	/**
	 * @inheritdoc
	 */
	public function currentBillTotalCost() {
	}

	/**
	 * @inheritdoc
	 */
	public function costPerKiloWatt() {
	}

	/**
	 * @inheritdoc
	 */
	public function costPerKiloWattHour() {
	}

	/**
	 * @inheritdoc
	 */
	public function demandDeltaSeconds() {
	}

	/**
	 * @inheritdoc
	 */
	public function demandDeltaMinutes() {
	}
}
