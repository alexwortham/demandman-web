<?php

namespace App\Services;

interface CostCaculator
{
	public function demandCost(array $curves);

	public function usageCost(array $curves);
}
