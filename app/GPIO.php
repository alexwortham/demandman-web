<?php

namespace App;

class GPIO
{
	/* Constants defined in gpiolib.h of bbb module. */
	const NO_EDGE = 0;
	const RISING_EDGE = 1;
	const FALLING_EDGE = 2;
	const BOTH_EDGE = 3;
	const INPUT = 0;
	const OUTPUT = 1;
	const ALT0 = 4;
	const HIGH = 1;
	const LOW = 0;
	const PUD_OFF = 0;
	const PUD_DOWN = 1;
	const PUD_UP = 2;
	private $pin;
	private $mode;
	private $value;
	private $pud;
	//more ...

	public function __construct($pin, $mode, $pud = self::PUD_OFF, $value = self::LOW) {
		$this->pin = $pin;
		$this->mode = $mode;
		$this->pud = $pud;
		$this->value = $value;

		return gpio_setup($pin, $mode, $pud, $value);
	}

	public function output($value) {

		$this->value = $value;

		return gpio_output($this->pin, $value);
	}

	public function input() {

		return gpio_input($this->pin);
	}

	public function get_mode() {

		return $mode;
	}

	/**
	 * WARNING! This will close all open GPIO's!!!! BAD!!!
	 */
	public function cleanup() {

		return gpio_cleanup();
	}
}
