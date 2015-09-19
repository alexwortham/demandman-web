<?php

	require_once("../app/PCF8574.php");
	
	use App\PCF8574;

function average($vals) {
	$count = count($vals);
	if ($count == 0) return 0.0;
	return array_sum($vals) / $count;
}

function get_demand($vals) {
	$new_curve = array();
	$dt = 15;
	$size = count($vals);
	for ($i = 0; $i < $size; $i += $dt) {
		$period = array_slice($vals, $i, $dt);
		$avg = average($period);
		$new_curve[] = $avg;
//		print "$i: $avg " . ($avg * $charge_fact) . "\n";
	}

//	foreach ($vals as $key => $val) {
//		$new_curve[$key] = $val * $val;
//	}

	return $new_curve;
}

function cmp_points($p1, $p2) {
	if ($p1[0] === $p2[0]) {
		return 0;
	}
	return $p1[0] > $p2[0];
}


function distribute_curve($points, $min, $max, $dt) {

	$new_points = array();
	$min = doubleval($min);
	$max = doubleval($max);
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

function reduce_curve($points, $dt) {

	$new_points = array();
	for ($i = 0; $i <= count($points); $i += $dt) {
		$new_points[strval($i)] = array();
	}

	foreach ($points as $key => $val) {
		$bucket = round(doubleval($key) / $dt) * $dt;
		$new_points[$bucket][] = $val;
	}
	foreach ($new_points as $key => $val) {
		$new_points[$key] = get_max($val);
	}

	return $new_points;
}

function scale_curve($points, $dt, $dv, $max) {

	$scaled_curve = array();
	foreach ($points as $key => $val) {
		$scaled_curve[strval( doubleval($key) / doubleval($dt) )] = ( round($val / doubleval($dv)) );// / doubleval($max) );
	}

	return $scaled_curve;
}

function read_curve($csv_file, $min, $max, $dt) {

	$points = array();

	while (($line = fgets($csv_file)) !== false) {
		$point_str = explode(",", $line);
		$points[] = array(doubleval(trim($point_str[0])) * 60, doubleval(trim($point_str[1])));
	}

	usort($points, "cmp_points");

	return distribute_curve($points, $min, $max, $dt);
}

function print_curve($points) {

	foreach ($points as $key => $val) {
		printf("%f, %f\n", doubleval($key), $val);
	}
}

function add_curves(&$curve_1, &$curve_2) {

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

function shift_curve(&$curve, $n, $dt) {

	$new_curve = array();
	foreach ($curve as $key => $val) {
		$new_curve[strval(doubleval($key) + $n * $dt)] = $val;
	}

	return $new_curve;
}

function get_max($vals) {
	$max = 0;
	foreach ($vals as $key => $val) {
		if ($val > $max) {
			$max = $val;
		}
	}

	return $max;
}

$curve1 = fopen($argv[1], "r");
$curve2 = fopen($argv[2], "r");
$min = doubleval($argv[3]);
$max = doubleval($argv[4]);
$dt =  doubleval($argv[5]);
$deadline = doubleval($argv[6]);

$dist_curve1 = read_curve($curve1, $argv[3], $argv[4], $argv[5]);
$reduced_curve1 = reduce_curve($dist_curve1, 60);
$scaled_curve1 = scale_curve($reduced_curve1, 60, 1000, 8);
$pcf = new PCF8574(1, 56);
foreach ($scaled_curve1 as $key => $val) {
	$pcf->set_range(0, intval($val) - 1);
	printf("%d, %d\n", $key, $val);
	usleep(1000000);
}
	$pcf->set_range(0, 0 - 1);
exit();
printf("$%.2f\n", get_max(get_demand($reduced_curve1)) * .008);

$reduced_curve2 = read_curve($curve2, $argv[3], $argv[4], $argv[5]);
printf("$%.2f\n", get_max(get_demand($reduced_curve2)) * .008);

$combined_curves = add_curves($reduced_curve1, $reduced_curve2);

$min_demand = NULL;
$max_demand = NULL;

for ($i = $min; $i <= $deadline; $i++) {
	$demand = get_demand(add_curves($reduced_curve1, shift_curve($reduced_curve2, $i, $dt)));
	foreach ($demand as $key => $val) {
		$charge = $val * .008;
	//	printf("%s => %.2f = $%.2f\n", $key, $val, $charge);
	}
	//print_curve($array);
	$demand_charge = get_max($demand) * .008;
	if ($min_demand == NULL || $demand_charge < $min_demand[1]) {
		$min_demand = array($i, $demand_charge);
	}
	if ($max_demand == NULL || $demand_charge > $max_demand[1]) {
		$max_demand = array($i, $demand_charge);
	}
	printf("Demand charge @ %d min: $%.2f\n", $i, $demand_charge);
}
	printf("Minimum demand charge occurs @ %d min: $%.2f\n", $min_demand[0], $min_demand[1]);
	printf("Maximum demand charge occurs @ %d min: $%.2f\n", $max_demand[0], $max_demand[1]);
//print_curve($reduced_curve1);

print "\n\n";

//print_curve($reduced_curve2);

print "\n\n";

//print_curve($combined_curves);

fclose($curve1);
fclose($curve2);
?>
