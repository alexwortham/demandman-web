<?php

/**
 * This class can do synchronous GPIO.
 */
namespace App;

/**
 * This class can do synchronous GPIO.
 */
class GPIO
{
	/** Constant defined in gpiolib.h of bbb.so module. */
	const NO_EDGE = 0;
	/** Constant defined in gpiolib.h of bbb.so module. */
	const RISING_EDGE = 1;
	/** Constant defined in gpiolib.h of bbb.so module. */
	const FALLING_EDGE = 2;
	/** Constant defined in gpiolib.h of bbb.so module. */
	const BOTH_EDGE = 3;
	/** Constant defined in gpiolib.h of bbb.so module. */
	const INPUT = 0;
	/** Constant defined in gpiolib.h of bbb.so module. */
	const OUTPUT = 1;
	/** Constant defined in gpiolib.h of bbb.so module. */
	const ALT0 = 4;
	/** Constant defined in gpiolib.h of bbb.so module. */
	const HIGH = 1;
	/** Constant defined in gpiolib.h of bbb.so module. */
	const LOW = 0;
	/** Constant defined in gpiolib.h of bbb.so module. */
	const PUD_OFF = 0;
	/** Constant defined in gpiolib.h of bbb.so module. */
	const PUD_DOWN = 1;
	/** Constant defined in gpiolib.h of bbb.so module. */
	const PUD_UP = 2;
	/**
	 * GPIO pin to operate.
	 *
	 * See gpiolib.h or bone101 for valid pin names. E.g. "P9_12".
	 *
	 * @var string $pin The name of the GPIO pin.
	 */
	private $pin;
	/**
	 * GPIO mode IN/OUT.
	 *
	 * Must be GPIO::INPUT or GPIO::OUTPUT.
	 *
	 * @var int $mode The GPIO mode to use.
	 */
	private $mode;
	/**
	 * The current value of the GPIO.
	 *
	 * Must be GPIO::HIGH or GPIO::LOW.
	 * 
	 * @var int $value The value of the GPIO.
	 */
	private $value;
	/**
	 * Pull up / Pull down.
	 * 
	 * Has something to do with INPUT mode. Must be GPIO::PUD_OFF, 
	 * GPIO::PUD_DOWN, or GPIO::PUD_UP.
	 * 
	 * @var int $pud Pud state.
	 */
	private $pud;

	/**
	 * Constructor
	 *
	 * Construct a new GPIO on pin $pin with mode $mode.
	 * Optionally set $pud and $value.
	 * 
	 * @param string $pin The pin name.
	 * @param int $mode The GPIO mode.
	 * @param int $pud The pud state.
	 * @param int $value The initial value.
	 */
	public function __construct($pin, $mode, $pud = self::PUD_OFF, $value = self::LOW) {
		$this->pin = $pin;
		$this->mode = $mode;
		$this->pud = $pud;
		$this->value = $value;

		gpio_setup($pin, $mode, $pud, $value);
	}

	/**
	 * Output a value to the GPIO.
	 *
	 * Must be GPIO::HIGH or GPIO::LOW.
	 *
	 * @param int $value The value to set.
	 * @return true|string True if successful or an error string.
	 */
	public function output($value) {

		$this->value = $value;

		return gpio_output($this->pin, $value);
	}

	/**
	 * Get input from the GPIO.
	 *
	 * @return int The value of the GPIO.
	 */
	public function input() {

		return gpio_input($this->pin);
	}

	/**
	 * Get the current mode of the GPIO.
	 *
	 * @return int The current mode.
	 */
	public function get_mode() {

		return $mode;
	}

	/**
	 * Cleanup all open GPIOs.
	 *
	 * **WARNING! This will close all open GPIO's!!!! BAD!!!**
	 * 
	 * @return true
	 */
	public function cleanup() {

		return gpio_cleanup();
	}
}
