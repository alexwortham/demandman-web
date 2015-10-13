<?php

namespace App\Console\Commands;

use Redis;
use Illuminate\Console\Command;
use Event;
use App\Events\AppStartAckEvent;
use App\Events\AppActionAckEvent;
use App\Events\AppActionFinEvent;
use Symfony\Component\Console\Input\InputArgument;
use App\Services\Meter;
use ReflectionClass;
use Exception;

class MeterServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meter:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the meter service for collecting load data.';

    protected $meter;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Meter $meter)
    {
        parent::__construct();
	$this->meter = $meter;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
	//$connection = Redis::connection('pubsub');
	//$connection->get('foo');
	try {
		Redis::psubscribe(['dm.response.*'], function($message, $channel) {
			$chan = explode('.', $channel);
			$evtType = ucfirst($chan[5]);
			$data = json_decode($message, true)['data'];
			$response = $data['actionResponse'];
		        echo "meter received message: $message\n";
			call_user_func_array([$this->meter, "app$evtType"],
				[$response['appId']]);
		});
	} catch (Exception $e) {
	    	printf("%s\n%s\n", $e->getMessage(), $e->getTraceAsString());
	}
    }
}

