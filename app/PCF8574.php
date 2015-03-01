<?php

namespace App;

class PCF8574
{
	/* Pin Constants */
	const P0 = 1;
	const P1 = 2;
	const P2 = 4;
	const P3 = 8;
	const P4 = 16;
	const P5 = 32;
	const P6 = 64;
	const P7 = 128;
	const PALL = 0xFF;
	const PNONE = 0;
	/* i2c slave address constants */
	const S0 = 56;
	const S1 = 57;
	const S2 = 58;
	const S3 = 59;
	const S4 = 60;
	const S5 = 61;
	const S6 = 62;
	const S7 = 63;
	public $bus;
	public $addr;
	private $is_bus_open = false;
	private static $pins = [1, 2, 4, 8, 16, 32, 64, 128];

	public function __construct($bus, $addr) {
		$this->bus = $bus;
		$this->addr = $addr;
	}

	public function set_pin($pin) {
		$val_now = $this->direct_read() ^ self::PALL;
		$new_val = $val_now | $pin;
		$this->direct_write($new_val ^ self::PALL);
	}

	public function unset_pin($pin) {
		$mask = self::PALL ^ $pin;
		$val_now = $this->direct_read() ^ self::PALL;
		$new_val = $val_now & $mask;
		$this->direct_write($new_val ^ self::PALL);
	}

	public function toggle_pin($pin) {
		$val_now = $this->direct_read() ^ self::PALL;
		$new_val = $val_now ^ $pin;
		$this->direct_write($new_val ^ self::PALL);
	}

	public function read_pin($pin) {
		$val_now = $this->direct_read() ^ self::PALL;
		$pin_now = $val_now & $pin;
		return ($pin_now & $pin === $pin);
	}

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

	private function open_bus_if_not_open() {
		if ($this->is_bus_open !== true) {
			$this->is_bus_open = ( i2c_open($this->bus) === true );
		}
	}
	
	public function direct_read() {
		$this->open_bus_if_not_open();

		return i2c_read_byte($this->addr);
	}

	public function direct_write($byte) {
		$this->open_bus_if_not_open();

		return i2c_write_byte($this->addr, $byte);
	}
}
