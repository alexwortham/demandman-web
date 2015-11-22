<?php 

/**
 * 
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\StartAppEvent;
use App\Events\StopAppEvent;
use App\Events\PauseAppEvent;
use App\Events\ResumeAppEvent;
use App\Events\WakeAppEvent;
use App\Events\AppActionEvent;
use Event;
use App\Model\Appliance;
use App\Services\ApplianceApi as Api;
use App\Services\ApiMessenger;
use Predis\Connection\ConnectionException;
use Redis;

/**
 * 
 */
class ApplianceController extends Controller {


	protected $api;
	protected $messenger;

	public function __construct(Api $api, ApiMessenger $messenger) {
		$this->api = $api;
		$this->messenger = $messenger;
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
