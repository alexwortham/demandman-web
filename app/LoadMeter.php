<?php

namespace App;

use App\PCF8574;

class LoadMeter
{
	public $name;
	public $min;
	public $max;
	private $pcf;
	private $load;

	public function __construct($name, $bus, $addr, $min, $max) {
		$this->name = $name;
		$this->min = $min;
		$this->max = $max;
		$this->pcf = new PCF8574($bus, $addr);
	}

	public function set_load($load) {
		return $this->pcf->set_range(0, $load - 1);
	}

	public function get_load() {
		return $this->load;
	}
}
