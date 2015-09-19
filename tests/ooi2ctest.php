<?php
	require_once("../app/PCF8574.php");

	use App\PCF8574;

	$pcf = new PCF8574(1, PCF8574::S0);

	$result = $pcf->direct_read();
	printf("result: $result\n");

	$result = $pcf->direct_write(255);
	printf("$result\n");
?>
