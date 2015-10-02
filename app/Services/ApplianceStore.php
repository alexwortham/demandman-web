<?php

/**
 * An interface for access to persistent storage of Appliances.
 */
namespace App\Services;

/**
 * An interface for access to persistent storage of Appliances.
 */
interface ApplianceStore
{
	/**
	 * Retrive an appliance from the storage backend by its id.
	 *
	 * @param mixed $appId The id of an appliance.
	 * @return App\Appliance An Appliance object.
	 */
	public function get($appId);
}
