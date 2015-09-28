<?php

namespace App;

class Simulator
{
	private $active = [];
	private $sleeping = [];
	public $stepTime = 1;
	public $currentStep = 0;

	public function appStart($appId) {
		$simulation = findSimulation($appId); /* Calls Stub!!! */
		$this->activate($appId, $simulation);
	}

	public function findSimulation($appId) {
		/* STUB!!! */
		return new Simulation();// bad constructor use
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
		$this->sleeping[$appId] = $this->active[$app];
		unset($this->active[$appId]);
	}

	private function wakeUp($appId) {
		$this->active[$appId] = $this->sleeping[$app];
		unset($this->sleeping[$appId]);
	}

	public function step() {
		foreach ($this->active as $appId => $sim) {
			$sim->step();
		}
		$this->currentStep++;
		sleep($stepTime);
	}

	public function reset() {
		foreach ($this->active as $appId => $sim) {
			$sim->reset();
		}
		foreach ($this->sleeping as $appId => $sim) {
			$sim->reset();
		}
		$this->currentStep = 0;
	}
}
