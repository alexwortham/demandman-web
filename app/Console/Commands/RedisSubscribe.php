<?php

namespace App\Console\Commands;

use Redis;
use Illuminate\Console\Command;
use Event;
use App\Events\AppStartAckEvent;
use ReflectionClass;
use Exception;

class RedisSubscribe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to a Redis channel';

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
	//$connection = Redis::connection('pubsub');
	//$connection->get('foo');
	try {
        Redis::psubscribe(['dm.*'], function($message, $channel) {
		$chan = explode('.', $channel);
		$evtType = ucfirst($chan[2]);
		$evtClass = sprintf("App\\Events\\App%sAckEvent", $evtType);
		$data = json_decode($message, true)['data'];
		$event = (new ReflectionClass($evtClass))->newInstanceArgs([$data]);
            echo "$message $evtClass\n";
		//Here, use reflection to load event class from the json data.
		//Just make all events accept associative array and boom, done.
		try {
		Event::fire($event);
		} catch (Exception $e) {
	    		printf("%s\n%s\n", $e->getMessage(), $e->getTraceAsString());
		}
        });
	} catch (Exception $e) {
	    	printf("%s\n%s\n", $e->getMessage(), $e->getTraceAsString());
	}
    }
}

