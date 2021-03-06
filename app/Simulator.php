<?php

namespace App;
use App\Model\Appliance;
use App\LoadMeter;
use App\Services\ApplianceApi as Api;
use App\Model\Simulation;

class Simulator
{
	private $active = [];
	private $sleeping = [];
	public $stepTime = 1;
	public $currentStep = 0;
	protected $api;

	public function __construct(Api $api) {
		$this->api = $api;
	}

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
				if ($sim->step() === false) {
					$this->api->stopAppliance($appId);
				}
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
