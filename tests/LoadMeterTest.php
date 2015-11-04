<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\LoadMeter;

class LoadMeterTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCalcValue()
    {
        $meter = new LoadMeter(1, 0x38, 500);
        $value = $meter->calc_value(750.5);
        $this->assertTrue($value === 2);
        $value = $meter->calc_value(749.9);
        $this->assertTrue($value === 1);
        $value = $meter->calc_value(500);
        $this->assertTrue($value === 1);
        $value = $meter->calc_value(4000);
        $this->assertTrue($value === 8);
        $value = $meter->calc_value(4001);
        $this->assertTrue($value === 8);
        $value = $meter->calc_value(0);
        $this->assertTrue($value === 0);
        $value = $meter->calc_value(-1);
        $this->assertTrue($value === 0);
    }
}
