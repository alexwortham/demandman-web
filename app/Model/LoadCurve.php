<?php

/**
 * Database Model class for LoadCurves.
 */
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\CurveFuncs;

/**
 * Database Model class for LoadCurves.
 *
 * @property string $name A name for this LoadCurve.
 * @property string $data The data for this curve in CSV format.
 * @property string $serialized_data The data for this curve in CSV format.
 * @property \App\Model\LoadData[] The LoadDatas associated with this LoadCurve.
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
	 * @var double[] $load_data The load data for this curve.
	 */
	protected $load_data = [];

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
	 * Create a new load curve with the given data.
	 *
	 * @param double[] $load_data An array of load data.
	 * @return \App\Model\LoadCurve A new LoadCurve object containing the given data.
	 */
	public static function createWithData($load_data) {
		$curve = new LoadCurve();
		$curve->load_data = $load_data;

		return $curve;
	}

	/**
	 * Drop all values after `$time` from the data.
	 *
	 * Thus giving you all data after `$time`.
	 *
	 * @param double|int $time The point after which all data is kept.
	 * @return \App\Model\LoadCurve A new LoadCurve containing only data after `$time`.
	 */
	public function dataAfter($time) {
		return LoadCurve::createWithData(
			array_filter($this->load_data, function ($t) use ($time) {
				return $t >= $time;
			}, ARRAY_FILTER_USE_KEY));
	}

	/**
	 * Set the load to `$watts` at time `$time`.
	 *
	 * @param int $time A Unix timestamp.
	 * @param double $watts The value to set at `$time`.
	 */
	public function setDataAt($time, $watts) {
		$this->load_data[$time] = $watts;
	}

	/**
	 * Add the value `$watts` to the value at time `$time` in this curve.
	 *
	 * @param mixed $time
	 * @param double $watts
	 * @return void
	 */
	public function addToData($time, $watts) {
		$this->setDataAt( $time, $this->getDataAt($time) + $watts );
	}

	/**
	 * Get the load data associated with a specific time in this curve.
	 *
	 * @param mixed $time The time to get load data at.
	 * @return double The load in watts at the given `$time`.
	 */
	public function getDataAt($time) {
		return $this->load_data[$time];
	}

	/**
	 * Serialize the data contained in this LoadCurve for saving to DB.
	 *
	 * @return void
	 */
	public function serialize_data() {
		$this->serialized_data = json_encode($this->load_data);
	}

	/**
	 * Unserialize the data saved in `$serialized_data`.
	 *
	 * @return void
	 */
	public function unserialize_data() {
		$this->load_data = json_decode($this->serialized_data);
	}

	function simulation() {
		return $this->hasOne('App\Model\Simulation');
	}

	function run() {
		return $this->hasOne('App\Model\Run');
	}

	/**
	 * Get the LoadDatas associated with this LoadCurve.
	 *
	 * @return \App\Model\LoadData[] An array of LoadData associated with this
	 * LoadCurve.
	 */
	public function loadData() {
		return $this->hasMany('App\Model\LoadData');
	}
}
