<?php

/**
 * Model class for individual appliance simulations.
 */
namespace App\Model;

use App\LoadMeter;
use App\CurveFuncs;

/**
 * Model class for individual appliance simulations.
 *
 * @property int $bus The I2C bus number to which the simulation device is attached.
 * @property int $addr The I2C address of the simulation device.
 */
class Simulation extends \Eloquent
{
	/**
	 * Number of watts per step.
	 */
	const STEP_SIZE = 500;
	public $currentStep = 0;
	private $count;
	/** @var  \App\LoadMeter $loadMeter */
	private $loadMeter;
	private $simCurve;

	public function activate() {
		$this->loadMeter = new LoadMeter(
			$this->bus,
			$this->addr,
			self::STEP_SIZE);
		$this->simCurve = $this->get_sim_curve();
		$this->count = count($this->simCurve);
	}

	public function step() {
		if ($this->currentStep < $this->count) {
			$this->loadMeter->set_load($this->simCurve[$this->currentStep]);
			$this->currentStep++;
			return true;
		} else if ($this->currentStep >= $this->count) {
			$this->loadMeter->set_load(0);
			$this->currentStep++;
			return false;
		}

		return false;
	}

	public function get_sim_curve() {
		$curve = $this->loadCurve->parse_data();
		//1 is the desired delta t of the distributed curve (1 second)
		$dist_curve = CurveFuncs::distribute_curve($curve, 0, 1);
		//60 is the desired delta t of the averaging window.
		return array_values(CurveFuncs::peakify($dist_curve, 60));
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
		return $this->belongsTo('App\Model\Appliance');
	}

	public function loadCurve() {
		return $this->belongsTo('App\Model\LoadCurve');
	}
}
