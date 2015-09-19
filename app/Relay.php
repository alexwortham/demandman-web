<?php

/**
 * This class drives a relay connected to a GPIO.
 */
namespace App;

use App\GPIO;

/**
 * This class drives a relay connected to a GPIO.
 */
class Relay
{
	/**
	 * Constant defining the ON state.
	 */
	const ON = 1;
	/**
	 * Constant defining the OFF state.
	 */
	const OFF = 0;
	/**
	 * Constant for setting GPIO mode to output.
	 */
	const GPIO_OUTPUT_MODE = 1;
	/**
	 * @var string $pin The pin name of the GPIO controlling the relay.
	 */
	private $pin;
	/**
	 * @var App\GPIO $gpio The GPIO controller object.
	 */
	private $gpio;
	/**
	 * @var int $state The current state: Relay::ON or Relay::OFF.
	 */
	private $state;

	/**
	 * Constructor
	 *
	 * Construct a Relay connected to the given GPIO pin and optionally
	 * set its initial state.
	 *
	 * @param string $pin The pin name of the GPIO controlling the relay.
	 * @param int $state The desired initial state of the relay.
	 */
	public function __construct($pin, $state = self::ON) {
		$this->state = $state;
		$this->gpio = new GPIO($pin, self::GPIO_OUTPUT_MODE);
		$this->gpio->output($state);
	}

	/**
	 * Set the relay to ON.
	 * 
	 * @return true|string True if successful or an error string.
	 */
	public function on() {
		$this->state = self::ON;

		return $this->gpio->output(self::ON);
	}

	/**
	 * Set the relay to OFF.
	 * 
	 * @return true|string True if successful or an error string.
	 */
	public function off() {
		$this->state = self::OFF;

		return $this->gpio->output(self::OFF);
	}

	/**
	 * Get the current state of the relay.
	 * 
	 * @return int The state of the relay.
	 */
	public function get_state() {

		return $this->state;
	}

	/**
	 * Get the name of the Relay's pin.
	 * 
	 * @return string The name of the Relay's pin.
	 */
	public function get_pin() {

		return $this->pin;
	}
}
