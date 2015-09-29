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

/**
 * 
 */
class ApplianceController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    
  }

	public function start($id) {
		$this->fireAppActionEvent(new StartAppEvent(Appliance::find($id)));
	}

	public function stop($id) {
		$this->fireAppActionEvent(new StopAppEvent(Appliance::find($id)));
	}

	public function pause($id) {
		$this->fireAppActionEvent(new PauseAppEvent(Appliance::find($id)));
	}

	public function resume($id) {
		$this->fireAppActionEvent(new ResumeAppEvent(Appliance::find($id)));
	}

	public function wake($id) {
		$this->fireAppActionEvent(new WakeAppEvent(Appliance::find($id)));
	}

	private function fireAppActionEvent(AppActionEvent $event) {
		$event->makeAppActionRequest();
		Event::fire($event);
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
