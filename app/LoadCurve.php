<?php

/**
 * Database Model class for LoadCurves.
 */
namespace App;

use Illuminate\Database\Eloquent\Model;
use App\CurveFuncs;

/**
 * Database Model class for LoadCurves.
 *
 * @property string $name A name for this LoadCurve.
 * @property string $data The data for this curve in CSV format.
 */
class LoadCurve extends Model
{
	/**
	 * @var string $table The name of the load curves table: 'load_curves'.
	 */
	protected $table = 'load_curves';
	/**
	 * @var boolean $timestamps Use timestamps.
	 */
	public $timestamps = true;

	/**
	 * Parse the curve data stored in this object.
	 *
	 * @return double[] An array of point data.
	 */
	public function parse_data() {

		return CurveFuncs::parse_data(
				preg_split("/((\r?\n)|(\r\n?))/", $this->data),
				60);
	}

	/**
	 * Drop all values after `$time` from the data.
	 *
	 * Thus giving you all data after `$time`.
	 *
	 * @param double|int $time The point after which all data is kept.
	 * @return App\LoadCurve A new LoadCurve containing only data after `$time`.
	 */
	public function dataAfter($time) {

	}

	public function appendToData($time, $watts) {

	}

	public function addToData($time, $watts) {

	}

	public function getDataAt($time) {

	}

	function simulation() {
		return $this->hasOne('App\Simulation');
	}

	function run() {
		return $this->hasOne('App\Run');
	}
}
