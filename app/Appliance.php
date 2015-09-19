<?php

/**
 * Database Model class for Appliances.
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Database Model class for Appliances.
 */
class Appliance extends Model {

	/**
	 * @var string $table The name of the appliances table: 'appliances'.
	 */
	protected $table = 'appliances';
	/**
	 * @var boolean $timestamps Use timestamps.
	 */
	public $timestamps = true;

	/**
	 * Returns the Sensor model associated with this Appliance.
	 *
	 * @return Sensor The sensor model associated with this Appliance.
	 */
	public function sensor()
	{
		return $this->hasOne('Sensor');
	}

}
