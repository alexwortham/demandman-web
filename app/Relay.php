<?php

namespace App;

use App\GPIO;

class Relay
{
	const ON = 1;
	const OFF = 0;
	const GPIO_OUTPUT_MODE = 1;
	private $pin;
	private $gpio;
	private $state;

	public function __construct($pin, $state = self::ON) {
		$this->state = $state;
		$this->gpio = new GPIO($pin, self::GPIO_OUTPUT_MODE);
		$this->gpio->output($state);
	}

	public function on() {
		$this->state = self::ON;

		return $this->gpio->output(self::ON);
	}

	public function off() {
		$this->state = self::OFF;

		return $this->gpio->output(self::OFF);
	}

	public function get_state() {

		return $this->state;
	}

	public function get_pin() {

		return $this->pin;
	}
}
