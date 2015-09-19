<?php

/**
 * Database Model Class for Sensors.
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Database Model Class for Sensors.
 */
class Sensor extends Model {

	/**
	 * @var string $table The name of the sensors table: 'sensors'.
	 */
	protected $table = 'sensors';
	/**
	 * @var boolean $timestamps Use timestamps.
	 */
	public $timestamps = true;

}
