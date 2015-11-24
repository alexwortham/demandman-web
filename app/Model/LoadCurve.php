<?php

/**
 * Database Model class for LoadCurves.
 */
namespace App\Model;

use App\CurveFuncs;
use \Carbon\Carbon;

/**
 * Database Model class for LoadCurves.
 *
 * @property string $name A name for this LoadCurve.
 * @property string $data The data for this curve in CSV format.
 * @property string $serialized_data The data for this curve in CSV format.
 */
class LoadCurve extends \Eloquent
{
	const SMOOTHING_ENABLED = true;
	const SMOOTHING_DISABLED = false;
	const SMOOTHING_FACTOR = 0.1;
	const SMOOTHING_THRESH = 500;

	/**
	 * @var string $table The name of the load curves table: 'load_curves'.
	 */
	protected $table = 'load_curves';
	/**
	 * @var boolean $timestamps Use timestamps.
	 */
	public $timestamps = true;

	/**
	 * @var \App\Model\LoadData[] $load_data The load data for this curve.
	 */
	public $load_data = [];

	/** @var bool $smoothing True if data added to the curve should be smoothed. */
	public $smoothing = self::SMOOTHING_ENABLED;

	/** @var int $prev_load The load of the most recently added point. */
	private $prev_load = 0;

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
	public function dataAfter(Carbon $time) {
		return LoadCurve::createWithData(
			array_filter($this->load_data, function ($t) use ($time) {
				return $t >= $time->timestamp;
			}, ARRAY_FILTER_USE_KEY));
	}

	/**
	 * Set the load to `$watts` at time `$time`.
	 *
	 * @param \Carbon\Carbon $time A Unix timestamp.
	 * @param \App\Model\LoadData $data The value to set at `$time`.
	 */
	public function setDataAt(Carbon $time, LoadData $data, $noSmoothing = false) {
		if ($noSmoothing !== true && $this->smoothing === self::SMOOTHING_ENABLED) {
			$this->expAverage($data);
		}
		$this->load_data[$time->timestamp] = $data;
	}

	/**
	 * Apply a specialized exponential averaging to the given data.
	 *
	 * Vanilla exponential averaging but the initial value is reset
	 * whenever the difference in the current and previous loads
	 * exceeds self::SMOOTHING_THRESH.
	 *
	 * @param LoadData $data
	 * @return void
	 */
	private function expAverage(LoadData $data) {
		if (abs($data->load - $this->prev_load) >= self::SMOOTHING_THRESH) {
			$this->prev_load = $data->load;
		} else {
			$this->prev_load = self::SMOOTHING_FACTOR * $data->load
				+ (1 - self::SMOOTHING_FACTOR) * $this->prev_load;
			$data->load = $this->prev_load;
		}
	}

	/**
	 * Set data over a specified range of seconds.
	 *
	 * @param Carbon $start First time to set.
	 * @param Carbon $end Ending time (not inclusive).
	 * @param LoadData $data The LoadData to copy across the range.
	 * @param int $delta The delta in seconds to move across the range.
	 * @return void
	 */
	public function setDataAtRange(Carbon $start, Carbon $end, LoadData $data, $delta = 1) {

		for ($it = $start->copy(); $it->timestamp < $end->timestamp; $it->addSeconds($delta)) {
			$new_data = $data->copyLD();
			$new_data->time = $it->copy();
			$this->load_data[$it->timestamp] = $new_data;
		}
	}

	/**
	 * Add a the portion of the given curve from start to end to this curve.
	 *
	 * @param Carbon $start Start adding from this time.
	 * @param Carbon $end Stop adding at end - 1.
	 * @param LoadCurve $curve The LoadCurve to add.
	 * @param int $delta The delta in seconds to move across the range.
	 */
	public function addToCurve(Carbon $start, Carbon $end, LoadCurve $curve, $delta = 1) {

		for ($it = $start->copy(); $it->timestamp < $end->timestamp; $it->addSeconds($delta)) {
			$new_data = $curve->getDataAt($it->timestamp);
			$our_data = $this->getDataAt($it->timestamp);
			if ($new_data !== NULL) {
                if ($our_data === NULL) {
					$new_data = $new_data->copyLD();
					$new_data->analog_current_monitor = NULL;
                    $this->setDataAt($it, $new_data);
                } else {
					$this->addToData($it, $new_data);
				}
			}
		}
	}

	/**
	 * Add the value `$watts` to the value at time `$time` in this curve.
	 *
	 * @param \Carbon\Carbon $time
	 * @param \App\Model\LoadData $data
	 * @return void
	 */
	public function addToData(Carbon $time, LoadData $data) {
		$this->getDataAt($time->timestamp)->addToLoad($data);
	}

	/**
	 * Get the load data associated with a specific time in this curve.
	 *
	 * @param mixed $time The time to get load data at.
	 * @return \App\Model\LoadData The load in watts at the given `$time`.
	 */
	public function getDataAt($time) {
		if (array_key_exists($time,$this->load_data)) {
			return $this->load_data[$time];
		} else {
			return NULL;
		}
	}

	/**
	 * Get the Simulation associated with this LoadCurve.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	function simulation() {
		return $this->hasOne('App\Model\Simulation');
	}

	/**
	 * Get the Run associated with this LoadCurve.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	function run() {
		return $this->hasOne('App\Model\Run');
	}

	/**
	 * Get the LoadDatas associated with this LoadCurve.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function loadData() {
		return $this->hasMany('App\Model\LoadData');
	}

	/**
	 * Save the model.
	 *
	 * Call the `saveMany()` method to save all LoadDatas stored in
	 * `$this->load_data`.
	 *
	 * @inheritdoc
	 */
	public function save(array $options = []) {

//		if (is_array($this->load_data) && count($this->load_data) > 0) {
//			$this->loadData()->saveMany($this->load_data);
//		}

		parent::save($options);
	}

}
