<?php

namespace App;

class CurveFuncs {

	public static function average($vals) {
		$count = count($vals);
		if ($count == 0) return 0.0;
		return array_sum($vals) / $count;
	}

	public static function get_demand($vals) {
		$new_curve = array();
		$dt = 15;
		$size = count($vals);
		for ($i = 0; $i < $size; $i += $dt) {
			$period = array_slice($vals, $i, $dt);
			$avg = self::average($period);
			$new_curve[] = $avg;
		}

		return $new_curve;
	}

	public static function cmp_points($p1, $p2) {
		if ($p1[0] === $p2[0]) {
			return 0;
		}

		return $p1[0] > $p2[0];
	}


	public static function distribute_curve($points, $min, $dt) {

		$new_points = array();
		$min = doubleval($min);
		$dt =  doubleval($dt);

		$x_new = $min;
		for ($i = 0, $j = 1; $j < count($points); $i++, $j++) {
			$x1 = $points[$i][0];
			$x2 = $points[$j][0];
			$y1 = $points[$i][1];
			$y2 = $points[$j][1];
			//printf("i: (%f, %f); j: (%f, %f)\n", $x1, $y1, $x2, $y2);
			$dx = ($x2 - $x1);
			if ($dx == 0) continue;
			$dy = ($y2 - $y1); 
			$m = $dy / $dx;
			$b = $y1 - ($m * $x1);
			for (; $x_new < $x2; $x_new += $dt) {
				$y_new = ($m * $x_new) + $b;
				//printf("New point: (%f, %f)\n", $x_new, $y_new);
				$new_points[strval($x_new)] = $y_new;
			}
		}

		return $new_points;

	}

	public static function reduce_curve($points, $dt) {

		$new_points = array();
		for ($i = 0; $i <= count($points); $i += $dt) {
			$new_points[strval($i)] = array();
		}

		foreach ($points as $key => $val) {
			$bucket = round(doubleval($key) / $dt) * $dt;
			$new_points[$bucket][] = $val;
		}
		foreach ($new_points as $key => $val) {
			$new_points[$key] = self::average($val);
		}

		return $new_points;
	}

	public static function scale_curve($points, $dt, $dv, $max = 1) {

		$scaled_curve = array();
		foreach ($points as $key => $val) {
			$scaled_curve[strval( doubleval($key) / doubleval($dt) )] = ( round($val / doubleval($dv)) / doubleval($max) );
		}

		return $scaled_curve;
	}

	public static function read_csv_file($csv_file, $min, $max, $dt) {

		$lines = array();

		while (($line = fgets($csv_file)) !== false) {
			$lines[] $line;
		}

		return $lines;
	}

	public static function parse_data($curve_data, $conv_fact = 1) {
		$data_array = array();

		foreach($curve_data as $line){
			//print  "$line<br />\n";
			$point_str = explode(",", $line);
			$data_array[] = array(doubleval(trim($point_str[0])) * $conv_fact, doubleval(trim($point_str[1])));
		} 

		usort($data_array, array(self, "cmp_points"));

		return $data_array;
	}

	public static function print_curve($points) {

		foreach ($points as $key => $val) {
			printf("%f, %f\n", doubleval($key), $val);
		}
	}

	public static function add_curves(&$curve_1, &$curve_2) {

		$new_curve = array();
		foreach ($curve_1 as $key => $val) {
			$new_curve[$key] = doubleval($val);
		}

		foreach ($curve_2 as $key => $val) {
			if (array_key_exists($key, $new_curve)) {
				$new_curve[$key] += doubleval($val);
			} else {
				$new_curve[$key] = doubleval($val);
			}
		}

		return $new_curve;
	}

	public static function shift_curve(&$curve, $n, $dt) {

		$new_curve = array();
		foreach ($curve as $key => $val) {
			$new_curve[strval(doubleval($key) + $n * $dt)] = $val;
		}

		return $new_curve;
	}

	public static function get_max($vals) {
		$max = 0;
		foreach ($vals as $key => $val) {
			if ($val > $max) {
				$max = $val;
			}
		}

		return $max;
	}

	public static function get_sim_curve($data) {
		//60 multiplies the x value by 60 (convert from mins to secs)
		$curve1 = self::parse_data($data, 60);
		//1 is the desired delta t of the distributed curve (1 second)
		$dist_curve1 = self::distribute_curve($curve1, 0, 1);
		//60 is the desired delta t of the averaging window.
		$reduced_curve1 = self::reduce_curve($dist_curve1, 60);
		//60 is the desired delta t (and therefore number of segments) of
		//the scaled curve. The x value will be divided by this number.
		//1000 is the scaling factor of the y axis. The y value will be 
		//diveded by this number (convert to kilowatts).
		//6 is the overall maximum value of the curve, used to scale the
		//y values between 0 and 6 in this case.
		$scaled_curve1 = self::scale_curve($reduced_curve1, 60, 1000, 6);
		self::print_curve($scaled_curve1);
	}
}
