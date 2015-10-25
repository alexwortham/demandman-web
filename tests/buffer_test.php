<?php

const CHAN_0 = 1;
const CHAN_1 = 2;
const CHAN_2 = 4;
const CHAN_3 = 8;
const CHAN_4 = 16;
const CHAN_5 = 32;
const CHAN_6 = 64;
const CHAN_7 = 128;

printf("opening buffer\n");

$status = adc_buffer_open(2048, CHAN_0 | CHAN_1 | CHAN_2 | CHAN_3);

sleep(1);

printf("opened buffer. status: %s\n", $status);

printf("reading buffer\n");


printf("read buffer. status: %s\n", $buffer);

printf("printing values\n");


//for ($i = 0; $i < $data_length; $i++) {
//	for ($j = 0; $j < $num_channels; $j++) {
//		printf("%d ", $buffer[$j][$i]);
//	}
//	printf("\n");
//}

$time = time();
$tick = 0;
$values = array();

for ($i = 0; $i < 4; $i++) {
	$values[$i] = array();
}

for ($x = 0; $x < 61; $x++) {
	$buffer = adc_buffer_read();

	//$num_channels = count($buffer);
	//$data_length  = count($buffer[0]);

	foreach ($buffer as $channel => $vals) {
		$sum = 0;
		$total = count($vals);
		for ($i = 0; $i < $total - 1; $i++) {
			if ($i > 0 && $i % 30 == 0) {
				$values[$channel][] = intval(round($sum / 30));
				$sum = 0;
			} else {
				$sum += $vals[$i];
			}
		}
		$sum += $vals[$total - 1];
		$values[$channel][] = intval(round($sum / ($total % 30)));
		unset($vals);
	}
	unset($buffer);
	$tick++;
	if ($tick > 14) {
		foreach ($values as $channel => $vals) {

			printf("%d@%d: %s\n", $channel, $time, json_encode($vals));
		}
		$values = array();
		$tick = 0;
	}
	$time = time() + 1;
	time_sleep_until($time);
}

adc_buffer_close();
