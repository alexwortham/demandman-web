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
        $curve = LoadCurve::find($sim->load_curve_id);
        $sim_curve = $sim->get_sim_curve();
        $prev_load = 0;
        $smoothed = new LoadCurve();
        $alpha = 0.1;
        foreach ($run->loadCurve->loadData as $point) {
            /* @var \App\Model\LoadData $point */
            if (abs($point->load - $prev_load) >= 500) {
                $prev_load = $point->load;
                $smoothed->setDataAt($point->time, $point->copyLD());
            } else {
                $prev_load = $alpha * $point->load + (1 - $alpha) * $prev_load;
                $newPt = $point->copyLD();
                $newPt->load = $prev_load;
                $smoothed->setDataAt($point->time, $newPt);
            }
        }
        return view('run/show', ['run' => $run, 'smoothed' => $smoothed, 'curve' => $sim_curve, 'live' => false]);
    }

    public function live($id)
    {
        $run = Run::find($id);
        $sim = Simulation::where('appliance_id', $run->appliance_id)->first();
        $curve = LoadCurve::find($sim->load_curve_id);
        $latestData = LoadData::where('load_curve_id', $run->load_curve_id)->get()->last();
        $latest = 0;
        if ($latestData !== NULL) {
            $latest = $latestData->time;
        }
        return view('run/show', ['run' => $run, 'curve' => $curve, 'smoothed' => new LoadCurve(),
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
