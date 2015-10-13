<?php

/**
 * This class reads values from an ADC.
 *
 * This class uses functions from the bbb.so extension to read from AIN devices.
 */
namespace App;

/**
 * This class reads values from an ADC.
 *
 * This class uses functions from the bbb.so extension to read from AIN devices.
 */
class Analog
{

	/**
	 * Which AIN to read from.
	 *
	 * An integer corresponding to the desired device name. E.g. AIN0.
	 *
	 * @var int $ain Which AIN to read from.
	 */
	public $ain;
	/**
	 * @var boolean $is_ain_open Whether or not the AIN device has been opened.
	 */
	private $is_ain_open = false;

	/**
	 * Constructor
	 *
	 * See $ain for valid arguments to this constructor.
	 *
	 * @param int $ain The AIN number to read from.
	 * @return void
	 */
	public function __construct($ain) {
		$this->ain = $ain;
	}

	/**
	 * Helper function
	 *
	 * Opens the AIN device if it is not already open.
	 *
	 * @return void
	 */
	private function open_ain_if_not_open() {
		if ($this->is_ain_open !== true) {
			$this->is_ain_open = ( setup_adc() === true );
		}
	}

	/**
	 * Read a value from the AIN device.
	 *
	 * Read a value from the AIN device specified by $ain.
	 *
	 * @return double|string A value between 0 and 1 or an error string.
	 */
	public function read() {
		$this->open_ain_if_not_open();
		return adc_read_value($this->ain);
	}

	/**
	 * Read a raw value from the AIN device.
	 * 
	 * Read a raw value from the AIN device specified by $ain.
	 * This will be a value between 0 and 1800. (milliVolts)
	 *
	 * @return double|string A value between 0 and 1800 or an error string.
	 */
	public function read_raw() {
		$this->open_ain_if_not_open();
		return adc_read_raw($this->ain);
	}
}
