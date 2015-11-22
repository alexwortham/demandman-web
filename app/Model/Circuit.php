<?php

/**
 * Database Model class for Circuits.
 */
namespace App\Model;
use App\PCF8574;

/**
 * Database Model class for Circuits.
 *
 * @property int $bus I2C bus number.
 * @property int $addr I2C bus slave address.
 * @property int $number Circuit number.
 * @property int $mask Mask of the corresponding I2C pin.
 * @property string $name A name for this circuit.
 */
class Circuit extends \Eloquent {

    /** @var \App\PCF8574 */
    private $pcf = NULL;

    public function open() {
        $this->setupPcf();
        $this->pcf->set_pin($this->mask);
    }

    public function close() {
        $this->setupPcf();
        $this->pcf->unset_pin($this->mask);
    }

    private function setupPcf() {
        if ($this->pcf === NULL) {
            $this->pcf = new PCF8574($this->bus, $this->addr);
        }
    }

    /**
     * Get the simulations associated with this appliance.
     *
     * @return \App\Model\Simulation[] An array of Simulations
     * associated with this appliance.
     */
    public function appliance() {
        return $this->belongsTo('App\Model\Appliance');
    }
}
