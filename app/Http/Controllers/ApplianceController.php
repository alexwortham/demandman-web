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
use App\Appliance;
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
		$foo = "idk";
		$chan = "idk";
		$kind = "idk";
		//get a client that has a timeout of 10 seconds.
		$redis = Redis::connection("pubsubconsumer");
		$pubsub = $redis->pubSubLoop();
		$pubsub->subscribe("dm.complete.appliance.1.action.Start");
		$timed_out = true;
		try {
			foreach ($pubsub as $message) {
				$chan = $message->channel;
				$foo = $message->payload;
				$kind = $message->kind;
				if ($message->kind === 'subscribe') {
					$this->api->startAppliance($id);
				}
				if ($message->kind === 'message') {
					$timed_out = false;
					break;
				}
			}
		} catch (ConnectionException $e) {
			//we timed out.
		}
		unset($pubsub);
		if ($timed_out) {
			return response()->json(['error' => ["type" => "timeout", "message" => "Operation timed out"]]);
		} else {
			return response($foo)->header('Content-Type', 'application/json');
		}
	}

	public function stop($id) {
		$this->api->stopAppliance($id);
	}

	public function pause($id) {
		$this->api->pauseAppliance($id);
	}

	public function resume($id) {
		$this->api->resumeAppliance($id);
	}

	public function wake($id) {
		$this->api->wakeAppliance($id);
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
