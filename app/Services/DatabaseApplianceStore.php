<?php

/**
 * An interface for access to persistent storage of Appliances.
 *
 * Uses the Eloquent ORM for storage.
 */
namespace App\Services;

use App\Model\Appliance;

/**
 * An interface for access to persistent storage of Appliances.
 *
 * Uses the Eloquent ORM for storage.
 */
class DatabaseApplianceStore implements ApplianceStore
{
	/**
	 * @inheritdoc
	 */
	public function get($appId) {
		return Appliance::find($appId);
	}
}
