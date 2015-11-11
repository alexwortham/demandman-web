<?php

/**
 * This class contains functions for manipulating load curves.
 */
namespace App;

/**
 * This class contains functions for manipulating load curves.
 */
class CurveFuncs
{

	/**
	 * Average the values of an array.
	 *
	 * @param int[]|double[] $vals An array of numbers for values.
	 * @return double An average of the values.
	 */
	public static function average($vals) {
		$count = count($vals);
		if ($count == 0) return 0.0;
		return array_sum($vals) / $count;
	}

	/**
	 * Calculate averages over a specified delta.
	 * 
	 * @param int[]|double[] $vals An array of numbers.
	 * @param int $dt A delta to average values across.
	 * @return double[] An array of the averages.
	 */
	public static function reduce_curve($vals, $dt) {
		$new_curve = array();
		$size = count($vals);
		for ($i = 0; $i < $size; $i += $dt) {
			$period = array_slice($vals, $i, $dt);
			$avg = self::average($period);
			$new_curve[strval($i)] = $avg;
		}

		return $new_curve;
	}

	/**
	 * Calculate the peak value over a specified delta.
	 *
	 * @param int[]|double[] $vals An array of numbers.
	 * @param int $dt A delta to peakify.
	 * @return int[]|double[] An array of peak values.
	 */
	public static function peakify($vals, $dt) {
		$new_curve = array();
		$size = count($vals);
		for ($i = 0; $i < $size; $i += $dt) {
			$period = array_slice($vals, $i, $dt);
			$avg = self::get_max($period);
			$new_curve[strval($i)] = $avg;
		}

		return $new_curve;
	}

	/**
	 * Callback function for comparing points by their x-value.
	 * 
	 * Used as an argument to usort to sort an array of points.
	 * Which is to say an array of arrays of the form:
	 * ```php
	 * $points = array(
	 *     array($x1, $y1),
	 *     array($x2, $y2),
	 *     ...
	 * );
	 * ```
	 * @param int[]|double[] $p1 The first point array.
	 * @param int[]|double[] $p2 The second point array.
	 * @return int 0 if the x-values are equal, 1 if `$p1 > $p2`, or -1 if `$p1 < $p2`.
	 */
	public static function cmp_points($p1, $p2) {
		if ($p1[0] === $p2[0]) {
			return 0;
		}

		return $p1[0] > $p2[0];
	}

	/**
	 * Uniformly distributes points over a specified delta.
	 * 
	 * The function takes the pairwise derivative of the `$points` and
	 * computes appropriate y-values for each x-value at intervals of `$dt`.
	 * `$points` is an array of the form:
	 * ```php
	 * $points = array(
	 *     array($x1, $y1),
	 *     array($x2, $y2),
	 *     ...
	 * );
	 * ```
	 * @param int[]|double[] $points An array of points.
	 * @param int $min The starting x-value. (Usually 0)
	 * @param int $dt The delta interval to compute new points at.
	 * @return int[]|double[] An array of the same form as $points where
	 * the points have been uniformly distributed.
	 */
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

	/**
	 * Scales points' values by specified deltas.
	 *
	 * Use this function to convert `$points` values as follows:
	 * - Divide the x-values by `$dt`.
	 * - Divide the y-values by `$dv`, and divide the result by $max.
	 * `$points` is an array of the form:
	 * ```php
	 * $points = array(
	 *     array($x1, $y1),
	 *     array($x2, $y2),
	 *     ...
	 * );
	 * ```
	 * 
	 * @param int[]|double[] $points An array of points.
	 * @param int|double $dt A value to divide the x-values by.
	 * @param int|double $dv A value to divide the y-values by.
	 * @param int|double $max A second factor to divide the modified y-values by.
	 * @return int[]|double[] An array of the same form as `$points` where
	 * the points have been scaled as detailed above.
	 */
	public static function scale_curve($points, $dt, $dv, $max = 1) {

		$scaled_curve = array();
		foreach ($points as $key => $val) {
			$scaled_curve[strval( doubleval($key) / doubleval($dt) )] = ( round($val / doubleval($dv))  );
		}

		return $scaled_curve;
	}

	/**
	 * Read a csv file into an array of string lines.
	 * 
	 * @param string $csv_file The name of a csv file.
	 * @return string[] An array containing the lines of the file.
	 */
	public static function read_csv_file($csv_file) {

		$lines = array();
		$file = fopen($csv_file, "r");

		while (($line = fgets($file)) !== false) {
			$lines[] = $line;
		}

		fclose($file);

		return $lines;
	}

	/**
	 * Parses string curve data into arrays of points.
	 * 
	 * Convert an array of strings with comma separated values into an array
	 * of points of the form:
	 * ```php
	 * $points = array(
	 *     array($x1, $y1),
	 *     array($x2, $y2),
	 *     ...
	 * );
	 * ```
	 * @param string[] $curve_data An array of comma separated strings.
	 * @param int|double $conv_fact A conversion factor to multiply the 
	 * x-values by.
	 * @return int[]|double[] An array of points as specified above.
	 */
	public static function parse_data($curve_data, $conv_fact = 1) {
		$data_array = array();

		foreach($curve_data as $line){
			//print  "$line<br />\n";
			$point_str = explode(",", $line);
			$data_array[] = array(doubleval(trim($point_str[0])) * $conv_fact, doubleval(trim($point_str[1])));
		} 

		usort($data_array, array(self::class, "cmp_points"));

		return $data_array;
	}

	/**
	 * Print point data in csv format.
	 * 
	 * `$points` is an array of the form:
	 * ```php
	 * $points = array(
	 *     array($x1, $y1),
	 *     array($x2, $y2),
	 *     ...
	 * );
	 * ```
	 * @param int[]|double[] $points An array of points.
	 * @return void
	 */
	public static function print_curve($points) {

		foreach ($points as $key => $val) {
			printf("%f, %f\n", doubleval($key), $val);
		}
	}

	/**
	 * Add two curves' points.
	 * 
	 * Desired result: `h(x) = f(x) + g(x)`.
	 *
	 * The function works as follows:
	 * 1. Create a new array for the result.
	 * 2. Loop over `$curve_1` and copy its values into the result array.
	 * 3. Loop over `$curve_2` and add its y-values to the corresponding
	 * y-values in the result array.
	 * 
	 * Though the function "works" when the domains of the curves are not
	 * *similar* the result may not have the desired effect. The best way
	 * way to ensure the curves are similar is to use the distribute_curve()
	 * function to filter the inputs to this function.
	 * 
	 * `$curve_1` and `$curve_2` are arrays of the form:
	 * ```php
	 * $curve = array(
	 *     array($x1, $y1),
	 *     array($x2, $y2),
	 *     ...
	 * );
	 * ```
	 *
	 * @see CurveFuncs::distribute_curve()  distribute_curve() function
	 * @param int[]|double[] $curve_1 An array of points.
	 * @param int[]|double[] $curve_2 An array of points.
	 * @return int[]|double[] A new array of points containing the result
	 * as specified above.
	 */
	public static function add_curves($curve_1, $curve_2) {

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

	/**
	 * Shift a curve's x-values by n deltas.
	 * 
	 * The function iterates over the points in the curve and applies the
	 * transformation `$x_new = $x + ($n * $dt)` and places the result
	 * into a new array.
	 * 
	 * `$curve` and the return value are of the form:
	 * ```php
	 * $curve = array(
	 *     array($x1, $y1),
	 *     array($x2, $y2),
	 *     ...
	 * );
	 * ```
	 * 
	 * @param int[]|double[] $curve An array of points.
	 * @param int $n The number of `$dt`'s to shift by.
	 * @param int $dt The delta to shift by.
	 * @return int[]|double[] A new array of points as specified above.
	 */
	public static function shift_curve($curve, $n, $dt) {

		$new_curve = array();
		foreach ($curve as $key => $val) {
			$new_curve[strval(doubleval($key) + $n * $dt)] = $val;
		}

		return $new_curve;
	}

	/**
	 * Get the maximum value from an array.
	 * 
	 * @param int[]|double[] An array of numbers.
	 * @return int|double The maximum value of `$vals`.
	 */
	public static function get_max($vals) {
		$max = 0;
		foreach ($vals as $key => $val) {
			if ($val > $max) {
				$max = $val;
			}
		}

		return $max;
	}

	/**
	 * Transform curve data into simulation format.
	 * 
	 * This is somewhat complex, I'll explain later.
	 * 
	 * @todo Document this function's behavior.
	 * 
	 * @param string[] $data An array of strings containing curve data.
	 * @return int[]|double[] An array of points in the format specified
	 * above.
	 */
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
