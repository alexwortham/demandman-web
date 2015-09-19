<?php
	require_once('../app/Analog.php');
	require_once('../app/PCF8574.php');
	require_once('../app/LoadMeter.php');
	use App\Analog;
	use App\LoadMeter;

	$analog = new Analog(0);
	//$meter = new LoadMeter("Fun meter", 0, 0, 0, 6);
	$vals = array();

	$start = microtime(true);
	for ($i = 0; $i < 120; $i++) {
		//$meter->set_load( $analog->read() );
		$val = array(strval(microtime(true)), $analog->read() * 1800);
		//$val = array(strval(microtime(true)), $meter->get_load());
		printf("%.6f Value: %s\n", doubleval($val[0]), $val[1]);
		sleep(1);
	}
	$end = microtime(true);
	$dt = $end - $start;
	printf("Time to execute %.6f\n", $dt);
	printf("Average sample rate %.6f\n", $dt / 120);


?>
