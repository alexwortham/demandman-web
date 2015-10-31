<?php

/**
 * Class for reading from analog buffers.
 */
namespace App;


/**
 * Class for reading from analog buffers.
 */
class BufferedAnalog
{

    /** Channel 0 mask */
    const CHAN_0 = 1;
    /** Channel 1 mask */
    const CHAN_1 = 2;
    /** Channel 2 mask */
    const CHAN_2 = 4;
    /** Channel 3 mask */
    const CHAN_3 = 8;
    /** Channel 4 mask */
    const CHAN_4 = 16;
    /** Channel 5 mask */
    const CHAN_5 = 32;
    /** Channel 6 mask */
    const CHAN_6 = 64;
    /** Channel 7 mask */
    const CHAN_7 = 128;

    /**
     * @var int $channels Bitmasked value of open channels.
     */
    public $channels;

    /**
     * @var int $length The length of the buffer.
     */
    public $length;

    /**
     * @var boolean $is_open If the buffer is open.
     */
    public $is_open;

    /**
     * @var mixed $last_read the value last read from the buffer.
     */
    public $last_read;

    /**
     * @var int $avg_length The number of values to average.
     */
    public $avg_length;

    /**
     * Open a buffer of a specified length on the given channels.
     *
     * @param int $length The desired length of the buffer.
     * @param int $channels Bitmasked value of channels to buffer.
     * @param int $avg_length The number of values to average.
     */
    public function __construct(int $length, int $channels, int $avg_length = 0) {
        $this->length = $length;
        $this->channels = $channels;
        $this->is_open = false;
        $this->avg_length = $avg_length;
    }

    /**
     * Open the buffer.
     *
     * @return bool|string True if successful, string containing an error otherwise.
     */
    public function open() {
        if ($this->is_open === false) {
            $status = adc_buffer_open($this->length, $this->channels);
            if ($status !== true) {
                return $status;
            }
        }

        return true;
    }

    /**
     * Return a multidimensional array containing values read from each channel.
     *
     * @return mixed An array of values or an error string.
     */
    public function read() {
        $buffer = adc_buffer_read();
        if (!is_array($buffer)) {
            return $buffer;
        }
        $values = array();

        for ($i = 0; $i < count($buffer); $i++) {
            $values[$i] = array();
        }

        if ($this->avg_length > 1) {
            foreach ($buffer as $channel => $vals) {
                $sum = 0;
                $total = count($vals);
                $i = 0;
                $remainder = $total % $this->avg_length;
                $num_vals = $total / $this->avg_length;
                if ($total >= $this->avg_length) {
                    for ($i = 0; $i < ($this->avg_length * $num_vals); $i++) {
                        if (($i > 0) && ($i % $this->avg_length == 0)) {
                            $values[$channel][] = intval(round( doubleval($sum) / $this->avg_length));
                            $sum = 0;
                        } else {
                            $sum += $vals[$i];
                        }
                    }
                }
                $sum = 0;
                if ($remainder > 0) {
                    for (; $i < $total; $i++) {
                        $sum += $vals[$i];
                    }
                    $values[$channel][] = intval(round( doubleval($sum) / $remainder));
                }
            }

            $this->last_read = $values;

        } else {
            $this->last_read = $buffer;
        }

        return $this->last_read;
    }

    /**
     * Close the buffer.
     *
     * @return boolean|string True if successful, string containing an error otherwise.
     */
    public function close() {
        return adc_buffer_close();
    }
}