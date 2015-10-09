<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Redis;
use App\Simulator;
use Event;
use App\Events\AppActionResponseEvent;

pcntl_signal(SIGUSR1, function($signo) {
	$connection = Redis::connection('pubsub');
	SimulatorCommand::$event = $connection->lpop(SimulatorCommand::$eventkey);
});

class SimulatorCommand extends Command
{

	public static $event = NULL;
	public static $pid = NULL;
	public static $eventkey = NULL;
	const SIM_PID_KEY = 'simpid';
	private static $simulator = NULL;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simulator:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the appliance simulator.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
	self::$pid = posix_getpid();
	self::$eventkey = 'process:'.self::$pid.':queue';
	$connection = Redis::connection('pubsub');
	$connection->set(self::SIM_PID_KEY, self::$pid);
	self::$simulator = new Simulator();

        while (true) {
		pcntl_signal_dispatch();
		if (self::$event !== NULL) {
			$this->handle_signal();
		}
		self::$simulator->step();
	}
    }

	private function handle_signal() {

		//do stuff to handle simulation changes
		$event = json_decode(self::$event, true);
		$action = ucfirst($event['data']['actionRequest']['action']);
		$appId = $event['data']['actionRequest']['appId'];
		$reqId = $event['data']['actionRequest']['id'];

		try {
			printf("Call app$action(%d)\n", $appId);
			call_user_func_array(array(self::$simulator, "app$action"), 
				array($appId));
			$response = array("status" => "successful", "appId" => $appId, "action" => $action, "requestId" => $reqId);
			Event::fire(new AppActionResponseEvent($response));
			self::$event = NULL;
		} catch (ErrorException $e) {
			printf("%s\n%s\n", $e->getMessage(), $e->getTraceAsString());
			$response = array("status" => "failed", "appId" => $appId, "action" => $action, "requestId" => $reqId);
			Event::fire(new AppActionResponseEvent($response));
		}
	}
}
