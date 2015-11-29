<?php

namespace App\Http\Controllers;

use App\Model\BillingCycle;
use App\Model\DemandHistory;
use App\Model\UserPreference;
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
        $costs = array();
        $appliances = Appliance::all();
        foreach ($appliances as $appliance) {
            $usage = array();
            $kwattHours = $appliance->demandHistories()
                ->where('billing_cycle_id', $bill->id)->sum('watt_hours');
            $kwattHours /= 1000;
            $kwattHours = round($kwattHours * 100) / 100;
            $usages[$appliance->id] = $kwattHours;

            $usageCharge = $appliance->demandHistories()
                ->where('billing_cycle_id', $bill->id)->sum('usage_charge');

            $costs[$appliance->id] = sprintf("$%.2f", $usageCharge);

        }
        $totalUsage = array_sum(array_values($usages));
        $demandData = $bill->demandHistories->first();
        $usageCost = $bill->demandHistories()
            ->where('usage_charge', '>', 0)
            ->whereNotNull('appliance_id')->sum('usage_charge');
        $stats = [
            ['title' => 'Total Energy Use',
                'subtitle' => 'Kilowatt Hours',
                'value' => $totalUsage],
            ['title' => 'Usage Cost',
                'subtitle' => 'Charge for kWh Used',
                'value' => sprintf("$%.2f", $usageCost)],
            ['title' => 'Peak Demand',
                'subtitle' => 'Kilowatts',
                'value' => sprintf("%.2f", $demandData->watts / 1000)],
            ['title' => 'Demand Charge',
                'subtitle' => 'Charge for Peak Demand',
                'value' => sprintf("$%.2f", $demandData->demand_charge)]
        ];
        $totalBill = sprintf("%.2f", $usageCost + $demandData->demand_charge);
        $threshold = UserPreference::where('name','demand.threshold')->first()->value;
        return view('main/index', ['appliances' => $appliances,
            'bill' => $bill, 'demand' => $demandData,
            'totalUsage' => $totalUsage, 'usages' => $usages,
            'stats' => $stats, 'totalBill' => $totalBill,
            'threshold' => $threshold, 'costs' => $costs]);
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
