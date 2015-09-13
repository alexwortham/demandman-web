<?php

namespace App;

class PCF8574
{
	const P0 = 1;
	const P1 = 2;
	const P2 = 4;
	const P3 = 8;
	const P4 = 16;
	const P5 = 32;
	const P6 = 64;
	const P7 = 128;
	const PALL = 0x7F;
	const PNONE = 0;
	public $bus;
	public $addr;
	private $is_bus_open = false;
	private static $pins = [1, 2, 4, 8, 16, 32, 64, 128];

	public function __construct($bus, $addr) {
		$this->bus = $bus;
		$this->addr = $addr;
	}

	public function set_pin($pin) {
		$val_now = $this->direct_read();
		$new_val = $val_now | $pin;
		$this->direct_write($new_val);
	}

	public function unset_pin($pin) {
		$mask = self::PALL ^ $pin;
		$val_now = $this->direct_read();
		$new_val = $val_now & $mask;
		$this->direct_write($new_val);
	}

	public function toggle_pin($pin) {
		$val_now = $this->direct_read();
		$new_val = $val_now ^ $pin;
		$this->direct_write($new_val);
	}

	public function read_pin($pin) {
		$val_now = $this->direct_read();
		$pin_now = $val_now & $pin;
		return ($pin_now & $pin === $pin);
	}

	public function set_range($start, $end) {
		if ($start < 0) return;
		if ($end < 0) {
			$this->direct_write(0);
			return;
		}
		$val = 0; 
		for ($pin = $start; $pin <= $end; $pin++) {
			$val = $val | self::$pins[$pin];
		}
		
		$this->direct_write($val);
	}

	private function open_bus_if_not_open() {
		if ($this->is_bus_open !== true) {
			$this->is_bus_open = ( i2c_open($this->bus) === true );
		}
	}
	
	public function direct_read() {
		open_bus_if_not_open();

		return i2c_read_byte($this->addr);
	}

	public function direct_write($byte) {
		open_bus_if_not_open();

		return i2c_write_byte($this->addr, $byte);
	}
}
