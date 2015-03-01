<?php namespace App\Http\Controllers;

use App\LoadMeter;

class LoadMeterController extends Controller {


	public function test($name, $bus, $addr, $min, $max) {

		return view('meter/test', ['name' => $name,
			'bus' => $bus,
			'addr' => $addr,
			'min' => $min,
			'max' => $max]);
	}

	public function set_meter($name, $bus, $addr, $min, $max, $val) {

		$meter = new LoadMeter($name, intval($bus), intval($addr), intval($min), intval($max));
		$return = $meter->set_load(intval($val));
		$load = $meter->get_load();

		return "{load: $load}";
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
	
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
	
	}

	/**
	 * Display the specified resource.
	 *
	 * @param	int	$id
	 * @return Response
	 */
	public function show($id)
	{
	
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param	int	$id
	 * @return Response
	 */
	public function edit($id)
	{
	
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param	int	$id
	 * @return Response
	 */
	public function update($id)
	{
	
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param	int	$id
	 * @return Response
	 */
	public function destroy($id)
	{
	
	}
	
}

?>
