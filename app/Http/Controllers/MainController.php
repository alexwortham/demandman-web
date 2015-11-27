<?php

namespace App\Http\Controllers;

use App\Model\BillingCycle;
use App\Model\DemandHistory;
use App\Services\CostCalculator;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\Appliance;

class MainController extends Controller
{
    /** @var  \App\Services\CostCalculator */
    protected $calculator;

    public function __construct(CostCalculator $calculator) {
        $this->calculator = $calculator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bill = BillingCycle::with(['demandHistories' => function($query){
            $query->orderBy('watts', 'desc')->take(1);
        }])->where('is_current', true)->get()->first();
        $usages = array();
        $appliances = Appliance::all();
        foreach ($appliances as $appliance) {
            $usage = $appliance->demandHistories()
                ->where('billing_cycle_id', $bill->id)->sum('watt_hours');
            $usage /= 1000;
            $usage = round($usage * 100) / 100;
            $usages[$appliance->id] = $usage;
        }
        $demandData = $bill->demandHistories->first();
        return view('main/index', ['appliances' => $appliances,
            'bill' => $bill, 'demand' => $demandData, 'usages' => $usages]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
