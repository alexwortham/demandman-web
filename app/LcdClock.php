<?php

/**
 * Class for creating an LCD clock.
 */
namespace App;

use \Carbon\Carbon;

/**
 * Class for creating an LCD clock.
 */
class LcdClock {

    /** Date format constant */
    const DATE_FORMAT = '  D, M. d';
    /** Time format constant */
    const TIME_FORMAT = '    h:i A';

    /** @var \App\LCD $lcd LCD driver. */
    private $lcd;

    /** @var \Carbon\Carbon $time The current time of the clock. */
    private $time;

    /** @var string $date_format Format string for date. */
    private $date_format = self::DATE_FORMAT;

    /** @var string $time_format Format string for time. */
    private $time_format = self::TIME_FORMAT;

    /**
     * Create a LcdClock.
     *
     * @param int $addr The I2C address of the LCD screen.
     */
    public function __construct($addr) {
        $this->lcd = new LCD($addr);
        $this->lcd->open();
    }

    /**
     * Print the date and time to the LCD.
     *
     * @param bool|false $clear True if lcd should be cleared first.
     */
    public function draw($clear = false) {
        if ($this->lcd->is_open === true) {
            $this->lcd->setCursor(0, 0);
            $this->lcd->printString($this->time->format($this->date_format), $clear);
            $this->lcd->setCursor(0, 1);
            $this->lcd->printString($this->time->format($this->time_format));
        }
    }

    /**
     * Set the time on the clock and refresh the LCD.
     *
     * @param Carbon $time The time to set the clock to.
     */
    public function setTime(Carbon $time) {
        $this->time = $time;
        $this->draw();
    }

    /**
     * Get the clock's current time.
     *
     * @return Carbon The time the clock is set to.
     */
    public function getTime() {
        return $this->time;
    }

    /**
     * Set the string used for formatting dates.
     *
     * @param string $dateFormat New date format string.
     */
    public function setDateFormat($dateFormat) {
        $this->date_format = $dateFormat;
    }

    /**
     * Set the string used for formatting times.
     *
     * @param string $timeFormat New time format string.
     */
    public function setTimeFormat($timeFormat) {
        $this->time_format = $timeFormat;
    }

    /**
     * Use this to call methods on the wrapped Carbon object.
     *
     * After the method is called the LCD will be updated.
     *
     * @param string $name The name of the method to call
     * @param mixed[] $args Arguments to the method.
     * @return mixed
     */
    public function __call($name, $args) {
        $ret = call_user_func_array([$this->time, $name], $args);
        $this->draw();
        return $ret;
    }
}
