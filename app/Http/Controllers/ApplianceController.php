<?php 

/**
 * 
 */
namespace App\Http\Controllers;

use App\Services\CostCalculator;
use App\Services\Predictor;
use Illuminate\Http\Request;
use App\Events\StartAppEvent;
use App\Events\StopAppEvent;
use App\Events\PauseAppEvent;
use App\Events\ResumeAppEvent;
use App\Events\WakeAppEvent;
use App\Events\AppActionEvent;
use Event;
use App\Model\Appliance;
use App\Model\Run;
use App\Services\ApplianceApi as Api;
use App\Services\ApiMessenger;
use Predis\Connection\ConnectionException;
use \Carbon\Carbon;
use Redis;

/**
 * 
 */
class ApplianceController extends Controller {


	protected $api;
	protected $messenger;
	protected $predictor;

	public function __construct(Api $api, ApiMessenger $messenger, Predictor $predictor) {
		$this->api = $api;
		$this->messenger = $messenger;
		$this->predictor = $predictor;
	}

	public function predict($id) {
		$redis = Redis::connection('pubsub');
		$startTime = Carbon::parse($redis->get('simulation:time'));
		$appliance = Appliance::find($id);
		$running = Run::with(['loadCurve.loadData' => function($query) {
				$query->orderBy('idx', 'desc')->take(1);
			}])->where('is_running', true)->get();
		$lastRun = Run::with('loadCurve', 'loadCurve.loadData')
			->where('appliance_id', $appliance->id)->orderBy('created_at', 'desc')->first();
		if ($lastRun === NULL) {
			//return 0;
		}
		$expectedCurve = $this->predictor->reindexCurve($startTime, $lastRun->loadCurve);
		$curves = array();
		$newTime = $startTime->copy();
		foreach ($running as $run) {
			$lastRun = Run::with('loadCurve', 'loadCurve.loadData')
				->where('appliance_id', $run->appliance_id)
				->where('is_running', false)
				->orderBy('created_at', 'desc')->first();
			$lastData = $run->loadCurve->loadData->first();
			$elapsed = 0;
			if ($lastData !== NULL) {
				$elapsed = $lastData->idx;
			}
			$newTime->modify((int ) $elapsed.' minutes ago');
			$reindexed = $this->predictor->reindexCurve($startTime, $lastRun->loadCurve, $elapsed);
			$curves[] = $reindexed;
		}
		$curves[] = $expectedCurve;

		$demands = $this->predictor->calculateDemands($startTime, [], $curves);


		return view('demands', ['demands' => $demands,
			'curves' => $curves, 'startTime' => $startTime, 'rel' => $newTime]);
	}

	public function predictOld($id) {
		$redis = Redis::connection('pubsub');
		//$time = Carbon::parse($redis->get('simulation:time'));
		$time = Carbon::now();
		$appliance = Appliance::find($id);
		$lastRun = Run::with('loadCurve', 'loadCurve.loadData')
			->where('appliance_id', $appliance->id)->orderBy('created_at', 'desc')->first();
		$lastRun2 = Run::with('loadCurve', 'loadCurve.loadData')
			->where('appliance_id', 2)->orderBy('created_at', 'desc')->first();
		//$curve = $this->predictor->reindexCurve($time, $lastRun->loadCurve);
		//$curve2 = $this->predictor->reindexCurve($time, $lastRun2->loadCurve);
		$curve = $lastRun->loadCurve;
		$curve2 = $lastRun2->loadCurve;
		//$demands = $this->predictor->calculateDemands($time, [$curve, $curve2], []);
		$demands = $this->predictor->calculateDemands($time, [], [$curve]);

		return view('demands', ['demands' => $demands, 'curve' => $curve]);
	}

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    
  }

	public function start($id) {
		$message = $this->subscribeAndWait("dm.complete.appliance.$id.action.Start",
			function () use ($id) {
				$this->api->startAppliance($id);
			});
		return $this->respondJsonOrTimeout($message);
	}

	public function stop($id) {
		$message = $this->subscribeAndWait("dm.complete.appliance.$id.action.Stop",
			function () use ($id) {
				$this->api->stopAppliance($id);
			});
		return $this->respondJsonOrTimeout($message);
	}

	public function pause($id) {
		$message = $this->subscribeAndWait("dm.complete.appliance.$id.action.Pause",
			function () use ($id) {
				$this->api->pauseAppliance($id);
			});
		return $this->respondJsonOrTimeout($message);
	}

	public function resume($id) {
		$message = $this->subscribeAndWait("dm.complete.appliance.$id.action.Resume",
			function () use ($id) {
				$this->api->resumeAppliance($id);
			});
		return $this->respondJsonOrTimeout($message);
	}

	public function wake($id) {
		$message = $this->subscribeAndWait("dm.complete.appliance.$id.action.Wake",
			function () use ($id) {
				$this->api->wakeAppliance($id);
			});
		return $this->respondJsonOrTimeout($message);
	}

	private function respondJsonOrTimeout($json) {
		if ($json === false) {
			return response()->json(['error' => ["type" => "timeout", "message" => "Operation timed out"]]);
		} else {
			return response($json)->header('Content-Type', 'application/json');
		}
	}

	private function subscribeAndWait($channel, $callable = null) {
		$redis = Redis::connection("pubsubconsumer");
		$pubsub = $redis->pubSubLoop();
		$pubsub->subscribe($channel);
		$timed_out = true;
		$chan = null;
		$msg = null;
		$kind = null;
		try {
			foreach ($pubsub as $message) {
				$chan = $message->channel;
				$msg = $message->payload;
				$kind = $message->kind;
				if ($message->kind === 'subscribe') {
					if ($callable !== null) {
						call_user_func($callable);
					}
				}
				if ($message->kind === 'message') {
					$timed_out = false;
					break;
				}
			}
		} catch (ConnectionException $e) {
			
		}
		$pubsub->unsubscribe();
		unset($pubsub);
		if ($timed_out === true) {
			return false;
		} else {
			return $msg;
		}
	}

	public function control() {
		$appliances = Appliance::all();
		return view('control', ['appliances' => $appliances]);
	}

	public function circuit($id, $state) {
		$appliance = Appliance::find($id);

		if ($state == "open") {
			$appliance->circuit->open();
		} else if ($state == "close") {
			$appliance->circuit->close();
		}

		return '{"status":"ok"}';
	}

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
      return view('appliances');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Request $request)
  {
    $appliance = new Appliance;
    $appliance->name = $request->name;
    $appliance->type = $request->type;

    $appliance->save();

    return view('appliances');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($id)
  {
    
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update($id)
  {
    
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($id)
  {
    
  }
  
}

?>
