<?php

	$success = i2c_open(1);
	printf("$success\n");

	$result = i2c_read_byte(56);
	printf("result: $result\n");

	$result = i2c_write_byte(56, 0);
	printf("$result\n");
?>
