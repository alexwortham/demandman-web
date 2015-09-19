<?php
	require_once("../app/GPIO.php");

	use App\GPIO;

	$gpio = new GPIO("P9_12", GPIO::OUTPUT, GPIO::PUD_OFF, 0);

	sleep(1);
	$value = 1;

	while (true) {

	$gpio->output($value);
	$value = $value ^ 1;

	sleep(5);

	}

?>
