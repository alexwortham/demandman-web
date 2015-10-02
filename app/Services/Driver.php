<?php

/**
 * The Driver interface actually physically controls appliances.
 */
namespace App\Services;

/**
 * The Driver interface actually physically controls appliances.
 */
interface Driver
{
	public function start();

	public function stop();

	public function pause();

	public function resume();

	public function wake();
}
