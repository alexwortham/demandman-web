<?php

/**
 * This class uses the PCF8574 driver to implement a load meter.
 */
namespace App;

use App\PCF8574;

/**
 * This class uses the PCF8574 driver to implement a load meter.
 */
class LoadMeter
{
	/**
	 * @var string $name A name for the load meter.
	 */
	public $name;
	/**
	 * A minimum value for the meter.
	 *
	 * Should be greater than or equal to 0.
	 *
	 * @var int $min A minimum value for the meter.
	 */
	public $min;
	/**
	 * A maximum value for the meter.
	 *
	 * Should be less than or equal to 8.
	 *
	 * @var int $max A maximum value for the meter.
	 */
	public $max;
	/**
	 * @var PCF8574 $pcf The pcf controller to use.
	 */
	private $pcf;
	/**
	 * The current load displayed by the meter.
	 *
	 * Should be `>= 0 && <= 8`.
	 *
	 * @var int $load The current load displayed by the meter.
	 */
	private $load;

	/**
	 * Constructor
	 *
	 * Construct a new load meter and instantiate `$pcf` based on the given
	 * parameters.
	 *
	 * @param string $name A name for the load meter.
	 * @param int $bus The i2c bus number of the PCF8574.
	 * @param int $addr The i2c bus address of the PCF8574.
	 * @param int $min The minimum value for the meter.
	 * @param int $max The maximum value for the meter. 
	 */
	public function __construct($name, $bus, $addr, $min, $max) {
		$this->name = $name;
		$this->min = $min;
		$this->max = $max;
		$this->pcf = new PCF8574($bus, $addr);
	}

	/**
	 * Set the load on the meter.
	 *
	 * Sets the internal `$load` variable and calls `$pcf->set_range()` to
	 * update the meter via i2c.
	 *
	 * @see PCF8574::set_range() PCF8574::set_range() function
	 * @param int $load A load from 0 to 8 to set.
	 * @return int|string The value written to the i2c bus or an error string.
	 */
	public function set_load($load) {
		$this->load = $load;
		return $this->pcf->set_range(0, $load - 1);
	}

	/**
	 * Get the current load of the meter.
	 * 
	 * @return int The current load.
	 */
	public function get_load() {
		return $this->load;
	}
}
