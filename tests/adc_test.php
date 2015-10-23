<?php
	require_once('../app/Analog.php');
	require_once('../app/PCF8574.php');
	require_once('../app/LoadMeter.php');
	use App\Analog;
	use App\LoadMeter;

	$analog = new Analog(0);
	$vals = array();

	if (count($argv) < 3) {
		fprintf(STDERR, "usage: adc_test.php [run time in seconds] [delay between reads in microseconds]\n");
		exit();
	}

	$start = microtime(true);
	for ($i = 0; $i < $argv[1] * 1000000; $i += $argv[2]) {
		$val = array(strval(microtime(true)), $analog->read() * 1800);
		printf("%.6f, %.6f\n", doubleval($val[0]), doubleval($val[1]));
		usleep($argv[2]);
	}
	$end = microtime(true);
	$dt = $end - $start;
	fprintf(STDERR, "Time to execute %.6f\n", $dt);
	fprintf(STDERR, "Average sample rate %.6f\n", $dt / $argv[1] * 1000000 / $argv[2]);


?>
