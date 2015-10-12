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

	function simulation() {
		return $this->hasOne('App\Simulation');
	}

	function run() {
		return $this->hasOne('App\Run');
	}
}
