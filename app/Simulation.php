<?php

namespace App;

use App\LoadMeter;
use App\Appliance;
use App\LoadCurve;
use Illuminate\Database\Eloquent\Model;

class Simulation extends Model
{
	public $currentStep = 0;
	private $count;
	private $loadMeter;

	public function __construct() {
	}

	public function activate() {
		$this->loadMeter = new LoadMeter($this->appliance->name,
			$this->bus,
			$this->addr,
			$this->min,
			$this->max);
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

	public function appliance() {
		return $this->belongsTo('App\Appliance');
	}

	public function loadCurve() {
		return $this->belongsTo('App\LoadCurve');
	}
}
