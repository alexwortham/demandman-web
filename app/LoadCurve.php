<?php

/**
 * Database Model class for LoadCurves.
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Database Model class for LoadCurves.
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
		$data_array = array();

		foreach(preg_split("/((\r?\n)|(\r\n?))/", $this->data) as $line){
			//print  "$line<br />\n";
			$point_str = explode(",", $line);
			$data_array[] = array(doubleval(trim($point_str[0])), doubleval(trim($point_str[1])));
		} 

		usort($data_array, array($this, "cmp_points"));

		return $data_array;
	}

	/**
	 * A comparator function for sorting points.
	 *
	 * @param double[] $p1 A point array.
	 * @param double[] $p2 A point array.
	 * @return int 0 if the points are equal, 1 if `$p1 > $p2`, else 0.
	 */
	function cmp_points($p1, $p2) {
		if ($p1[0] === $p2[0]) {
			return 0;
		}
		return $p1[0] > $p2[0];
	}

	function simulation() {
		return $this->hasOne('App\Simulation');
	}
}
