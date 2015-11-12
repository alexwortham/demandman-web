<?php

namespace App;

use \Carbon\Carbon;

class LcdClock {

    const CLOCK_12HOUR = 12;
    const CLOCK_24HOUR = 24;

    /** @var \App\LCD $lcd LCD driver. */
    private $lcd;

    /** @var \Carbon\Carbon $time The current time of the clock. */
    private $time;
}