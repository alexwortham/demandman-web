<?php

/**
 * Model class for individual appliance simulations.
 */
namespace App;

use App\LoadMeter;
use App\Appliance;
use App\LoadCurve;
use App\CurveFuncs;
use Illuminate\Database\Eloquent\Model;

/**
 * Model class for individual appliance simulations.
 *
 * @property int $bus The I2C bus number to which the simulation device is attached.
 * @property int $addr The I2C address of the simulation device.
 * @property int $min The minimum value of the simulation device.
 * @property int $max The maximum value of the simulation device.
 */
class Simulation extends Model
{
	public $currentStep = 0;
	private $count;
	private $loadMeter;
	private $simCurve;

	public function __construct() {
	}

	public function activate() {
		$this->loadMeter = new LoadMeter($this->appliance->name,
			$this->bus,
			$this->addr,
			$this->min,
			$this->max);
		$this->simCurve = $this->get_sim_curve();
		$this->count = count($this->simCurve);
	}

	public function step() {
		if ($this->currentStep < $this->count) {
			$this->loadMeter->set_load($this->simCurve[$this->currentStep]);
			$this->currentStep++;
			return true;
		} else if ($this->currentStep === $this->count) {
			$this->loadMeter->set_load(0);
			$this->currentStep++;
			return false;
		}

		return false;
	}

	private function get_sim_curve() {
		$curve = $this->loadCurve->parse_data();
		//1 is the desired delta t of the distributed curve (1 second)
		$dist_curve = CurveFuncs::distribute_curve($curve, 0, 1);
		//60 is the desired delta t of the averaging window.
		$reduced_curve = CurveFuncs::reduce_curve($dist_curve, 60);
		//60 is the desired delta t (and therefore number of segments) of
		//the scaled curve. The x value will be divided by this number.
		//1000 is the scaling factor of the y axis. The y value will be 
		//diveded by this number (convert to kilowatts).
		//6 is the overall maximum value of the curve, used to scale the
		//y values between 0 and 6 in this case.
		return CurveFuncs::scale_curve($reduced_curve, 60, 1000, $this->max);
	}

	public function reset() {
		$this->currentStep = 0;
		$this->loadMeter->set_load($this->simCurve[$this->currentStep]);
	}

	public function sleep() {
		$this->loadMeter->set_load(0);
	}

	public function wakeUp() {
		$this->loadMeter->set_load($this->simCurve[$this->currentStep]);
	}

	public function appliance() {
		return $this->belongsTo('App\Appliance');
	}

	public function loadCurve() {
		return $this->belongsTo('App\LoadCurve');
	}
}
