<?php

namespace App;

use App\LoadMeter;

class Simulation
{
	public $currentStep = 0;
	private $loadMeter;
	private $simCurve;
	private $count;

	public function __construct(LoadMeter $loadMeter, $simCurve) {
		$this->loadMeter = $loadMeter;
		$this->simCurve = $simCurve;
		$this->count = count($simCurve);
	}

	public function step() {
		if ($this->currentStep < $this->count) {
			$this->loadMeter->set_load($simCurve[$this->currentStep]);
			$this->currentStep++;
		} else if ($this->currentStep === $this->count) {
			$this->loadMeter->set_load(0);
			$this->currentStep++;
		}
	}

	public function reset() {
		$this->currentStep = 0;
		$this->loadMeter->set_load($simCurve[$this->currentStep]);
	}

	public function sleep() {
		$this->loadMeter->set_load(0);
	}

	public function wakeUp() {
		$this->loadMeter->set_load($simCurve[$this->currentStep]);
	}
}
