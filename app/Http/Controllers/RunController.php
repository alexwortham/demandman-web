<?php

/**
 *
 */
namespace App\Http\Controllers;

use App\Model\Simulation;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\LoadCurve;
use App\Model\LoadData;
use App\Model\Run;
use App\CurveFuncs;

/**
 *
 */
class RunController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        return view('run/index', ['runs' => Run::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //return view('curves/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
//        $curve = new LoadCurve;
//
//        $curve->name = $request->name;
//        $curve->data = $request->data;
//
//        $curve->save();
//
//        return view('curves/create');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $run = Run::find($id);
        $sim = Simulation::where('appliance_id', $run->appliance_id)->first();
        $curve = LoadCurve::with('loadData')->find($run->load_curve_id);
        $sim_curve = $sim->get_sim_curve();
        return view('run/show', ['run' => $run, 'smoothed' => $curve, 'curve' => $sim_curve, 'live' => false]);
    }

    public function data($id)
    {
        $run = Run::find($id);
        $sim = Simulation::where('appliance_id', $run->appliance_id)->first();
        $curve = LoadCurve::with('loadData')->find($run->load_curve_id);
        $sim_curve = $sim->get_sim_curve();
        $run_curve = array();
        foreach ($curve->loadData as $point) {
            $run_curve[] = $point->load;
        }
        return view('run/data', ['run' => $run, 'smoothed' => $run_curve, 'curve' => $sim_curve]);
    }

    public function live($id)
    {
        $run = Run::find($id);
        $sim = Simulation::where('appliance_id', $run->appliance_id)->first();
        $curve = LoadCurve::with('loadData')->find($run->load_curve_id);
        $sim_curve = $sim->get_sim_curve();
        $latestData = LoadData::where('load_curve_id', $run->load_curve_id)->get()->last();
        $latest = 0;
        if ($latestData !== NULL) {
            $latest = $latestData->time;
        }
        return view('run/show', ['run' => $run, 'curve' => $sim_curve,
            'smoothed' => $curve,
            'live' => true, 'since' => $latest]);
    }

    public function liveUpdate($id, $since) {
        $run = Run::find($id);
        $date = Carbon::parse($since);
        return LoadData::where('load_curve_id', $run->load_curve_id)
            ->where('time', '>', $date)->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //return view('curves/edit', ['curve' => LoadCurve::find($id)]);
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
//        $curve = LoadCurve::find($id);
//
//        $curve->name = $request->name;
//        $curve->data = $request->data;
//
//        $curve->save();
//
//        return view('curves/show', ['curve' => $curve]);
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
}
