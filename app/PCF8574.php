<?php
/**
 * This class controls a PCF8574 chip via i2c.
 *
 * This class uses functions from the bbb.so extension to control a PCF8574 chip.
 */
namespace App;

/**
 * This class controls a PCF8574 chip via i2c.
 *
 * This class uses functions from the bbb.so extension to control a PCF8574 chip.
 */
class PCF8574
{
	/**
	 * Pin Constant
	 * 
	 * Refers to the 'P0' pin.
	 */
	const P0 = 1;
	/**
	 * Pin Constant
	 * 
	 * Refers to the 'P1' pin.
	 */
	const P1 = 2;
	/**
	 * Pin Constant
	 * 
	 * Refers to the 'P2' pin.
	 */
	const P2 = 4;
	/**
	 * Pin Constant
	 * 
	 * Refers to the 'P3' pin.
	 */
	const P3 = 8;
	/**
	 * Pin Constant
	 * 
	 * Refers to the 'P4' pin.
	 */
	const P4 = 16;
	/**
	 * Pin Constant
	 * 
	 * Refers to the 'P5' pin.
	 */
	const P5 = 32;
	/**
	 * Pin Constant
	 * 
	 * Refers to the 'P6' pin.
	 */
	const P6 = 64;
	/**
	 * Pin Constant
	 * 
	 * Refers to the 'P7' pin.
	 */
	const P7 = 128;
	/**
	 * Pin Constant
	 * 
	 * Sets all pins to 1.
	 */
	const PALL = 0xFF;
	/**
	 * Pin Constant
	 * 
	 * Sets all pins to 0.
	 */
	const PNONE = 0;
	/**
	 * Slave Address
	 * 
	 * Refers to i2c bus slave address 000.
	 */
	const S0 = 56;
	/**
	 * Slave Address
	 * 
	 * Refers to i2c bus slave address 001.
	 */
	const S1 = 57;
	/**
	 * Slave Address
	 * 
	 * Refers to i2c bus slave address 010.
	 */
	const S2 = 58;
	/**
	 * Slave Address
	 * 
	 * Refers to i2c bus slave address 011.
	 */
	const S3 = 59;
	/**
	 * Slave Address
	 * 
	 * Refers to i2c bus slave address 100.
	 */
	const S4 = 60;
	/**
	 * Slave Address
	 * 
	 * Refers to i2c bus slave address 101.
	 */
	const S5 = 61;
	/**
	 * Slave Address
	 * 
	 * Refers to i2c bus slave address 110.
	 */
	const S6 = 62;
	/**
	 * Slave Address
	 * 
	 * Refers to i2c bus slave address 111.
	 */
	const S7 = 63;
	/**
	 * i2c bus number of the PCF8574
	 * 
	 * Refers to i2c bus number to which the PCF8674 you wish to communicate
	 * with is connected. On the BeagleBone Black this can be 0 or 1. This 
	 * corresponds directly with the i2c device names found in the /dev/ tree.
	 * So 0 corresponds to /dev/i2c-0, and 1 to /dev/i2c-1.
	 *
	 * @var int $bus i2c bus number of the PCF8574
	 */
	public $bus;
	/**
	 * i2c bus slave address of the PCF8574
	 *
	 * Refers to the i2c bus slave address of the PCF8574 you wish to
	 * communicate with. This must be set to one of the constants
	 * specified in this class. S0, S1, S2, S3, S4, S5, S6, or S7.
	 *
	 * @var int $addr i2c bus slave address of the PCF8574
	 */
	public $addr;
	/**
	 * Tracks whether or not the i2c bus has been opened.
	 *
	 * @var boolean $_is_bus_open whether or not the i2c bus has been opened
	 */
	private $is_bus_open = false;
	/**
	 * Array for programmatic access to the pin constants.
	 */
	private static $pins = [1, 2, 4, 8, 16, 32, 64, 128];

	/**
	 * Constructor
	 *
	 * See the documentation for the $bus and $addr properties for valid
	 * values this constructor will accept.
	 * 
	 * @param int $bus The i2c bus to connect to.
	 * @param int $addr The i2c slave address to use.
	 */
	public function __construct($bus, $addr) {
		$this->bus = $bus;
		$this->addr = $addr;
	}

	/**
	 * Set a pin's value to 1.
	 *
	 * This will set the specified pin's value to 1. Valid args are the
	 * P* constants from this class. P0, P1, P2, P3, P4, P5, P6, P7.
	 * 
	 * @param int $pin The pin to set.
	 * @return void
	 */
	public function set_pin($pin) {
		$val_now = $this->direct_read() ^ self::PALL;
		$new_val = $val_now | $pin;
		$this->direct_write($new_val ^ self::PALL);
	}

	/**
	 * Set a pin's value to 0.
	 *
	 * This will set the specified pin's value to 0. Valid args are the
	 * P* constants from this class. P0, P1, P2, P3, P4, P5, P6, P7.
	 * 
	 * @param int $pin The pin to unset.
	 * @return void
	 */
	public function unset_pin($pin) {
		$mask = self::PALL ^ $pin;
		$val_now = $this->direct_read() ^ self::PALL;
		$new_val = $val_now & $mask;
		$this->direct_write($new_val ^ self::PALL);
	}

	/**
	 * Flip a pin's value.
	 *
	 * This will flip the specified pin's value. Valid args are the
	 * P* constants from this class. P0, P1, P2, P3, P4, P5, P6, P7.
	 * 
	 * @param int $pin The pin to flip.
	 * @return void
	 */
	public function toggle_pin($pin) {
		$val_now = $this->direct_read() ^ self::PALL;
		$new_val = $val_now ^ $pin;
		$this->direct_write($new_val ^ self::PALL);
	}

	/**
	 * Read a pin's value.
	 *
	 * This will read the specified pin's value. Valid args are the
	 * P* constants from this class. P0, P1, P2, P3, P4, P5, P6, P7.
	 * 
	 * @param int $pin The pin to flip.
	 * @return boolean True if the pin is set to 1, false otherwise.
	 */
	public function read_pin($pin) {
		$val_now = $this->direct_read() ^ self::PALL;
		$pin_now = $val_now & $pin;
		return ($pin_now & $pin === $pin);
	}

	/**
	 * Set a contiguous range of pins to 1.
	 *
	 * This will set all pins between $start (inclusive) and $end (inclusive)
	 * to 1. Valid args are the
	 * P* constants from this class. P0, P1, P2, P3, P4, P5, P6, P7.
	 * 
	 * @param int $start The first pin to set.
	 * @param int $end The last pin to set.
	 * @return int|string The value passed to direct_write() or an error string.
	 */
	public function set_range($start, $end) {
		if ($start < 0) return "start < 0";
		if ($end < 0) {
			$this->direct_write(self::PALL);
			return "end < 0";
		}
		if ($end >= 7) {
			$this->direct_write(self::PNONE);
			return "end >= 7";
		}
		$val = 0; 
		for ($pin = $start; $pin <= $end; $pin++) {
			$val = $val | self::$pins[$pin];
		}
		
		$this->direct_write($val ^ self::PALL);
		return $val ^ self::PALL;
	}

	/**
	 * Open the i2c bus if it's not already open.
	 *
	 * @return void
	 */
	private function open_bus_if_not_open() {
		if ($this->is_bus_open !== true) {
			$this->is_bus_open = ( i2c_open($this->bus) === true );
		}
	}
	
	/**
	 * Read the value from the PCF8574.
	 *
	 * **WARNING!** This function does not XOR the returned value.
	 *
	 * @return int The value returned from reading the PCF8574.
	 */
	public function direct_read() {
		$this->open_bus_if_not_open();

		return i2c_read_byte($this->addr);
	}

	/**
	 * Write a value to the PCF8574.
	 *
	 * **WARNING!** This function does not XOR the given value.
	 *
	 * @param int $byte The value to write.
	 * @return boolean|string True if the write was successful, 
	 * else an error string.
	 */
	public function direct_write($byte) {
		$this->open_bus_if_not_open();

		return i2c_write_byte($this->addr, $byte);
	}
}
