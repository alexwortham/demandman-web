<?php

/**
 * 
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\LoadCurve;
use App\CurveFuncs;

/**
 * 
 */
class LoadCurveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
	return view('curves/index', ['curves' => LoadCurve::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('curves/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $curve = new LoadCurve;

	$curve->name = $request->name;
	$curve->data = $request->data;

	$curve->save();

	return view('curves/create');
	
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return view('curves/show', ['curve' => LoadCurve::find($id)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        return view('curves/edit', ['curve' => LoadCurve::find($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
	$curve = LoadCurve::find($id);

	$curve->name = $request->name;
	$curve->data = $request->data;

	$curve->save();

        return view('curves/show', ['curve' => $curve]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function reduce($id, $min, $max, $dt)
    {
	$curve = LoadCurve::find($id);
	$reduced = $this->reduce_curve($curve, $min, $max, $dt);
        return view('curves/reduce', ['curve' => $curve, 'demand' => $reduced]);
    }

	public function calculate($c1, $c2, $min, $max, $dt, $dl) {
		$min_demand = NULL;
		$max_demand = NULL;
		$demand_charges = array();
		$curve1 = LoadCurve::find($c1);
		$curve2 = LoadCurve::find($c2);
		$reduced_curve1 = $this->reduce_curve($curve1, $min, $max, $dt);
		$reduced_curve2 = $this->reduce_curve($curve2, $min, $max, $dt);

		for ($i = $min; $i <= $dl; $i++) {
			$shifted_curve = $this->shift_curve($reduced_curve2, $i, $dt);
			$added_curve = $this->add_curves($reduced_curve1, $shifted_curve);
			$demand = $this->get_demand($added_curve);
			$demand_charge = $this->get_max($demand) * .008;
			$demand = array($i, $demand_charge, $added_curve, $reduced_curve1, $shifted_curve);
			$demand_charges[] = $demand;
			if ($min_demand == NULL || $demand_charge < $min_demand[1]) {
				$min_demand = $demand;
			}
			if ($max_demand == NULL || $demand_charge > $max_demand[1]) {
				$max_demand = $demand;
			}
		}
	
		$combined_plot = array();

		foreach ($min_demand[2] as $key => $val) {
			$triple = [0, 0, 0];
			$triple[0] = $val;
			if (array_key_exists($key, $min_demand[3])) {
				$triple[1] = $min_demand[3][$key];
			}
			if (array_key_exists($key, $min_demand[4])) {
				$triple[2] = $min_demand[4][$key];
			}
			$combined_plot[] = $triple;
		}

		return view('curves/calculate', ['curve1' => $curve1, 'curve2' => $curve2, 
				'demand' => $demand_charges, 
				'min' => $min_demand, 'max' => $max_demand, 
				'combined' => $combined_plot]);
	}
}
