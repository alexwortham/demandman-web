<?php

/**
 * An implementation of the CostCalculator interface.
 */
namespace App\Services;

use App\Model\BillingCycle;
use App\Model\LoadCurve;
use App\Model\ElectricityRate;

/**
 * An implementation of the CostCalculator interface.
 */
class BasicCostCalculator implements CostCalculator
{
	/** @var \App\Model\ElectricityRate $rate */
	public static $rate;
	/** @var \App\Model\BillingCycle */
	public static $billingCycle;

	public function __construct() {
		self::$rate = ElectricityRate::where('is_current', true)->first();
		self::$billingCycle = BillingCycle::where('is_current', true)->first();
	}
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
		return self::$rate->demand_rate;
	}

	/**
	 * @inheritdoc
	 */
	public function costPerKiloWattHour() {
		return self::$rate->usage_rate;
	}

	/**
	 * @inheritdoc
	 */
	public function demandDeltaSeconds() {
		return self::$rate->demand_delta * 60;
	}

	/**
	 * @inheritdoc
	 */
	public function demandDeltaMinutes() {
		return self::$rate->demand_delta;
	}

	/**
	 * @inheritdoc
	 */
	public function getCurrentBillingCycle() {
		return self::$billingCycle;
	}
}
