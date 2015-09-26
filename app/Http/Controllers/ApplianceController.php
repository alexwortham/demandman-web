<?php 

/**
 * 
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\StartAppEvent;
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
		Event::fire(new StartAppEvent(Appliance::find($id)));
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
