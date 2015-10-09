<?php

namespace App\Services;

interface CostCalculator
{
	public function demandCost(array $curves);

	public function usageCost(array $curves);
}
