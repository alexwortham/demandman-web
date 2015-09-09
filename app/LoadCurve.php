<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoadCurve extends Model
{
	protected $table = 'load_curves';
	public $timestamps = true;

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

	function cmp_points($p1, $p2) {
		if ($p1[0] === $p2[0]) {
			return 0;
		}
		return $p1[0] > $p2[0];
	}
}
