<?php

namespace App;
use App\Appliance;
use App\LoadMeter;

class Simulator
{
	private $active = [];
	private $sleeping = [];
	public $stepTime = 1;
	public $currentStep = 0;

	public function appStart($appId) {
		$simulation = $this->findSimulation($appId);
		$this->activate($appId, $simulation);
	}

	public function findSimulation($appId) {
		return Appliance::find($appId)->simulations->first();
	}

	public function appStop($appId) {
		$this->deactivate($appId);
	}

	public function appPause($appId) {
		$this->putToSleep($appId);
	}

	public function appResume($appId) {
		$this->wakeUp($appId);
	}

	private function activate($appId, Simulation $sim) {
		$sim->activate();
		$this->active[$appId] = $sim;
	}

	private function deactivate($appId) {
		if (array_key_exists($appId, $this->active)) {
			unset($this->active[$appId]);
		}
		if (array_key_exists($appId, $this->sleeping)) {
			unset($this->sleeping[$appId]);
		}
	}

	private function putToSleep($appId) {
		$this->sleeping[$appId] = $this->active[$appId];
		$this->active[$appId] = null;
	}

	private function wakeUp($appId) {
		$this->active[$appId] = $this->sleeping[$appId];
		$this->sleeping[$appId] = null;
	}

	public function step() {
		foreach ($this->active as $appId => $sim) {
			if ($sim !== null) {
				$sim->step();
			}
		}
		$this->currentStep++;
		sleep($this->stepTime);
	}

	public function reset() {
		foreach ($this->active as $appId => $sim) {
			if ($sim !== null) {
				$sim->reset();
			}
		}
		foreach ($this->sleeping as $appId => $sim) {
			if ($sim !== null) {
				$sim->reset();
			}
		}
		$this->currentStep = 0;
	}
}
