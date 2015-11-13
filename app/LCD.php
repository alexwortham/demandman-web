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
        return lcd_print($string);
    }

    public function open() {
        if ($this->is_open === false) {
            if (lcd_begin($this->addr) === true) {
                $this->is_open = true;
            }
        }
    }

    public function home() {
        return lcd_home();
    }

    public function clear() {
        return lcd_clear();
    }

    public function setCursor($col, $row) {
        if ($col <= self::COLS && $row <= self::ROWS) {
            return lcd_set_cursor_position($col, $row);
        }
    }

    public function backlight() {
        return lcd_set_backlight(self::ON);
    }

    public function noBacklight() {
        return lcd_set_backlight(self::OFF);
    }

    public function display() {
        return lcd_set_display(self::ON);
    }

    public function noDisplay() {
        return lcd_set_display(self::OFF);
    }

    public function blink() {
        return lcd_set_blink(self::ON);
    }

    public function noBlink() {
        return lcd_set_blink(self::OFF);
    }

    public function cursor() {
        return lcd_set_cursor(self::ON);
    }

    public function noCursor() {
        return lcd_set_cursor(self::OFF);
    }

    public function autoscroll() {
        return lcd_set_autoscroll(self::ON);
    }

    public function noAutoscroll() {
        return lcd_set_autoscroll(self::OFF);
    }

    public function scrollDisplayLeft() {
        return lcd_scroll_display(self::SCROLL_LEFT);
    }

    public function scrollDisplayRight() {
        return lcd_scroll_display(self::SCROLL_RIGHT);
    }

    public function leftToRight() {
        return lcd_set_direction(self::LEFT2RIGHT);
    }

    public function rightToLeft() {
        return lcd_set_direction(self::RIGHT2LEFT);
    }
}
