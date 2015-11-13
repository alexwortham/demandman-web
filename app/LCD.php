<?php

/**
 * An LCD driver class.
 */
namespace App;

/**
 * An LCD driver class.
 */
class LCD {

    /** Constant that indicates scroll cursor left */
    const SCROLL_LEFT = -1;
    /** Constant that indicates scroll cursor right */
    const SCROLL_RIGHT = 1;
    /** Constant that indicates text is left to right */
    const LEFT2RIGHT = 1;
    /** Constant that indicates text is right to left */
    const RIGHT2LEFT = -1;
    /** Number of rows on LCD screen */
    const ROWS = 2;
    /** Number of columns on LCD screen */
    const COLS = 16;
    /** Constant for turning LCD controls on */
    const ON = true;
    /** Constant for turning LCD controls off */
    const OFF = false;

    /** @var  int $addr I2C slave address of LCD. */
    public $addr;

    /** @var bool $is_open True if the LCD screen is open. */
    public $is_open;

    /**
     * Construct a new LCD instance with the given I2C address.
     *
     * @param $addr
     */
    public function __construct($addr) {
        $this->addr = $addr;
        $this->is_open = false;
    }

    /**
     * Print a string to the LCD.
     *
     * @param string $string The string to print.
     * @param bool|false $clear If true clear the screen before printing.
     * @return mixed
     */
    public function printString($string, $clear = false) {
        if ($clear !== false) {
            $this->clear();
        }
        return lcd_print($string);
    }

    /**
     * Calls the underlying LCD initialization function.
     *
     * @return void
     */
    public function open() {
        if ($this->is_open === false) {
            if (lcd_begin($this->addr) === true) {
                $this->is_open = true;
            }
        }
    }

    /**
     * Move the cursor home.
     *
     * @return mixed
     */
    public function home() {
        return lcd_home();
    }

    /**
     * Clear the screen.
     *
     * @return mixed
     */
    public function clear() {
        return lcd_clear();
    }

    /**
     * Set the cursor position.
     *
     * @param int $col Desired column position.
     * @param int $row Desired row position.
     * @return mixed
     */
    public function setCursor($col, $row) {
        if ($col <= self::COLS && $row <= self::ROWS) {
            return lcd_set_cursor_position($col, $row);
        }

        return false;
    }

    /**
     * Turn on the backlight.
     *
     * @return mixed
     */
    public function backlight() {
        return lcd_set_backlight(self::ON);
    }

    /**
     * Turn off the backlight.
     *
     * @return mixed
     */
    public function noBacklight() {
        return lcd_set_backlight(self::OFF);
    }

    /**
     * Turn on the display.
     *
     * @return mixed
     */
    public function display() {
        return lcd_set_display(self::ON);
    }

    /**
     * Turn off the display.
     *
     * @return mixed
     */
    public function noDisplay() {
        return lcd_set_display(self::OFF);
    }

    /**
     * Turn on cursor blink.
     *
     * @return mixed
     */
    public function blink() {
        return lcd_set_blink(self::ON);
    }

    /**
     * Turn off cursor blink.
     *
     * @return mixed
     */
    public function noBlink() {
        return lcd_set_blink(self::OFF);
    }

    /**
     * Turn on the cursor.
     *
     * @return mixed
     */
    public function cursor() {
        return lcd_set_cursor(self::ON);
    }

    /**
     * Turn off the cursor.
     *
     * @return mixed
     */
    public function noCursor() {
        return lcd_set_cursor(self::OFF);
    }

    /**
     * Turn on autoscroll.
     *
     * @return mixed
     */
    public function autoscroll() {
        return lcd_set_autoscroll(self::ON);
    }

    /**
     * Turn off autoscroll.
     *
     * @return mixed
     */
    public function noAutoscroll() {
        return lcd_set_autoscroll(self::OFF);
    }

    /**
     * Scroll the display left.
     *
     * @return mixed
     */
    public function scrollDisplayLeft() {
        return lcd_scroll_display(self::SCROLL_LEFT);
    }

    /**
     * Scroll the display right.
     *
     * @return mixed
     */
    public function scrollDisplayRight() {
        return lcd_scroll_display(self::SCROLL_RIGHT);
    }

    /**
     * Set text direction to left to right.
     *
     * @return mixed
     */
    public function leftToRight() {
        return lcd_set_direction(self::LEFT2RIGHT);
    }

    /**
     * Set text direction to right to left.
     *
     * @return mixed
     */
    public function rightToLeft() {
        return lcd_set_direction(self::RIGHT2LEFT);
    }
}
