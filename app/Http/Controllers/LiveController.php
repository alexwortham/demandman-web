<?php

/**
 *
 */
namespace App\Http\Controllers;

use App\Services\CostCalculator;
use App\Services\Predictor;
use Illuminate\Http\Request;
use App\Events\StartAppEvent;
use App\Events\StopAppEvent;
use App\Events\PauseAppEvent;
use App\Events\ResumeAppEvent;
use App\Events\WakeAppEvent;
use App\Events\AppActionEvent;
use Event;
use App\Model\Appliance;
use App\Model\Run;
use App\Services\ApplianceApi as Api;
use App\Services\ApiMessenger;
use Predis\Connection\ConnectionException;
use \Carbon\Carbon;
use Redis;

/**
 *
 */
class LiveController extends Controller {


    protected $api;
    protected $messenger;
    protected $predictor;

    public function __construct(Api $api, ApiMessenger $messenger, Predictor $predictor) {
        $this->api = $api;
        $this->messenger = $messenger;
        $this->predictor = $predictor;
    }

    public function demand() {
        $running = Run::with('loadCurve')->where('is_running', true)->get();

        $demand = 0;
        $demands = array();
        $appsOn = array();
        foreach($running as $run) {
            $loadData = $run->loadCurve->loadData()->orderBy('idx', 'desc')->first();
            $appsOn[] = $run->appliance_id;
            if ($loadData !== NULL) {
                $demands[] = ["load" => $loadData, "appId" => $run->appliance_id];
                $demand += $loadData->load;
            }
        }

        $appsOff = array();
        $notRunning = NULL;
        if (count($appsOn) > 0) {
            $notRunning = Appliance::whereNotIn('id', $appsOn)->get();
        } else {
            $notRunning = Appliance::all();
        }
        foreach ($notRunning as $notRun) {
            $appsOff[] = $notRun->id;
        }

        $demand /= 1000;

        return response()->json(['demand' => sprintf("%.2f", $demand),
            'demands' => $demands, 'appsOn' => $appsOn, 'appsOff' => $appsOff]);
    }

}

?>
