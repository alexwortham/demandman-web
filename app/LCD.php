<?php

namespace App;

class LCD {

    const SCROLL_LEFT = -1;
    const SCROLL_RIGHT = 1;
    const LEFT2RIGHT = 1;
    const RIGHT2LEFT = -1;
    const ROWS = 2;
    const COLS = 16;
    const ON = true;
    const OFF = false;

    /** @var  int $addr I2C slave address of LCD. */
    public $addr;

    public $is_open;

    public function __construct($addr) {
        $this->addr = $addr;
        $this->is_open = false;
    }

    public function printString($string, $clear = false) {
        if ($clear !== false) {
            $this->clear();
        }
        lcd_print($string);
    }

    public function open() {
        if ($this->is_open === false) {
            if (lcd_begin($this->addr) === true) {
                $this->is_open = true;
            }
        }
    }

    public function home() {
        lcd_home();
    }

    public function clear() {
        lcd_clear();
    }

    public function setCursor($col, $row) {
        if ($col <= self::COLS && $row <= self::ROWS) {
            lcd_set_cursor_position($col, $row);
        }
    }

    public function backlight() {
        lcd_set_backlight(self::ON);
    }

    public function noBacklight() {
        lcd_set_backlight(self::OFF);
    }

    public function display() {
        lcd_set_display(self::ON);
    }

    public function noDisplay() {
        lcd_set_display(self::OFF);
    }

    public function blink() {
        lcd_set_blink(self::ON);
    }

    public function noBlink() {
        lcd_set_blink(self::OFF);
    }

    public function cursor() {
        lcd_set_cursor(self::ON);
    }

    public function noCursor() {
        lcd_set_cursor(self::OFF);
    }

    public function autoscroll() {
        lcd_set_autoscroll(self::ON);
    }

    public function noAutoscroll() {
        lcd_set_autoscroll(self::OFF);
    }

    public function scrollDisplayLeft() {
        lcd_scroll_display(self::SCROLL_LEFT);
    }

    public function scrollDisplayRight() {
        lcd_scroll_display(self::SCROLL_RIGHT);
    }

    public function leftToRight() {
        lcd_set_direction(self::LEFT2RIGHT);
    }

    public function rightToLeft() {
        lcd_set_direction(self::RIGHT2LEFT);
    }
}