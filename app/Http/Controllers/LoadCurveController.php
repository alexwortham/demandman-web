<?php

/**
 * 
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\LoadCurve;

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

	function average($vals) {
		$count = count($vals);
		if ($count == 0) return 0.0;
		return array_sum($vals) / $count;
	}

	function get_demand($vals) {
		$new_curve = array();
		$dt = 15;
		$size = count($vals);
		for ($i = 0; $i < $size; $i += $dt) {
			$period = array_slice($vals, $i, $dt);
			$avg = $this->average($period);
			$new_curve[] = $avg;
	//		print "$i: $avg " . ($avg * $charge_fact) . "\n";
		}

	//	foreach ($vals as $key => $val) {
	//		$new_curve[$key] = $val * $val;
	//	}

		return $new_curve;
	}
    

	public function reduce_curve($curve, $min, $max, $dt) {

		$points = $curve->parse_data();
		$new_points = array();
		$min = doubleval($min);
		$max = doubleval($max);
		$dt =  doubleval($dt);
		for ($i = $min; $i <= $max; $i += $dt) {
			$new_points[strval($i)] = array();
		}

		$i = 0;
		foreach ($new_points as $key => $val) {
			for (; $points[$i][0] < (doubleval($key) + doubleval($dt) / 2) && $i < count($points) - 1; $i++) {
				$val[] = $points[$i][1];
			}
			$new_points[$key] = $this->average($val);
		}

		return $new_points;
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

	function print_curve($points) {

		foreach ($points as $key => $val) {
			printf("(%f, %f)\n", doubleval($key), $val);
		}
	}

	function add_curves(&$curve_1, &$curve_2) {

		$new_curve = array();
		foreach ($curve_1 as $key => $val) {
			$new_curve[$key] = doubleval($val);
		}

		foreach ($curve_2 as $key => $val) {
			if (array_key_exists($key, $new_curve)) {
				$new_curve[$key] += doubleval($val);
			} else {
				$new_curve[$key] = doubleval($val);
			}
		}

		return $new_curve;
	}

	function shift_curve(&$curve, $n, $dt) {

		$new_curve = array();
		foreach ($curve as $key => $val) {
			$new_curve[strval(doubleval($key) + $n * $dt)] = $val;
		}

		return $new_curve;
	}

	function get_max($vals) {
		$max = 0;
		foreach ($vals as $key => $val) {
			if ($val > $max) {
				$max = $val;
			}
		}

		return $max;
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
