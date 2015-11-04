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
	 * Number of steps supported by the meter.
	 */
	const NUM_STEPS = 8;
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
	 * @var int $load The current load displayed by the meter.
	 */
	public $load;
	/**
	 * @var int $inc Increment per step.
	 */
	public $inc;
	/**
	 * The value currently displayed by the meter.
	 *
	 * Should be `>= 0 && <= 8`.
	 *
	 * @var int The value currently displayed by the meter.
	 */
	public $value;

	/**
	 * Constructor
	 *
	 * Construct a new load meter and instantiate `$pcf` based on the given
	 * parameters.
	 *
	 * @param int $bus The i2c bus number of the PCF8574.
	 * @param int $addr The i2c bus address of the PCF8574.
	 * @param int $min The minimum value for the meter.
	 * @param int $max The maximum value for the meter.
	 * @param int $inc The increment to be displayed per step.
	 */
	public function __construct($bus, $addr, $inc) {
		$this->min = 0;
		$this->max = $inc * self::NUM_STEPS;
		$this->inc = $inc;
		$this->value = 0;
		$this->load = 0;
		$this->pcf = new PCF8574($bus, $addr);
	}

	/**
	 * Set the load on the meter.
	 *
	 * Sets the internal `$load` variable and calls `$pcf->set_range()` to
	 * update the meter via i2c.
	 *
	 * @see PCF8574::set_range() PCF8574::set_range() function
	 * @param double $load A load to set.
	 * @return int|string The value written to the i2c bus or an error string.
	 */
	public function set_load($load) {
		$this->calc_value($load);

		return $this->pcf->set_range(0, $this->value - 1);
	}

	/**
	 * Calculate the meter's internal value for a given load.
	 *
	 * @param double $load A load to calculate the meter value for.
	 * @return int The calculated meter value.
	 */
	public function calc_value($load) {
		if ($load < $this->min) {
			$this->load = 0;
		} else if ($load > $this->max) {
			$load = $this->max;
		}
		$this->load = $load;
		$this->value = intval( round($this->load / $this->inc) );

		return $this->value;
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
