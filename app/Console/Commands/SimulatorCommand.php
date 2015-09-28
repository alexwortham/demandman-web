<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Redis;
use App\Simulator;

pcntl_signal(SIGUSR1, function($signo) {
	echo "Got SIGUSR1\n";
	PcntlTest::$event = Redis::lpop(PcntlTest::$eventkey);
});

class SimulatorCommand extends Command
{

	public static $event = NULL;
	public static $pid;
	public static $eventkey;
	const SIM_PID_KEY = 'simpid';
	private $simulator;

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
	self::$pid = posix_getpid();
	self::$eventkey = 'process:'.self::$pid.':queue';
	Redis::set(SIM_PID_KEY, self::$pid);
	$this->simulator = new Simulator();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        while (true) {
		pcntl_signal_dispatch();
		if (self::$event !== NULL) {
			$this->handle_signal();
		}
		//$this->simulator->step();
		printf("Step\n");
	}
    }

	private function handle_signal() {

		//do stuff to handle simulation changes
		printf("%s\n", self::$event);
		$event = json_decode(self::$event, true);
		$action = $event['data']['appActionRequest']['action'];
		$appId = $event['data']['appActionRequest']['appId'];

		//call_user_func_array(array($this->simulator, "app$action"), 
		//	array($appId));
		printf("Call app$action\n");
	}
}
