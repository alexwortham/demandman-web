<?php

namespace App;

class Analog
{
	public $ain;
	private $is_ain_open = false;

	public function __construct($ain) {
		$this->ain = $ain;
	}

	private function open_ain_if_not_open() {
		if ($this->is_ain_open !== true) {
			$this->is_ain_open = ( setup_adc() === true );
		}
	}

	public function read() {
		$this->open_ain_if_not_open();
		return adc_read_value($this->ain);
	}
}
