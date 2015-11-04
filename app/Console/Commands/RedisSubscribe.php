<?php

namespace App\Console\Commands;

use Redis;
use Illuminate\Console\Command;
use Event;
use App\Events\AppStartAckEvent;
use App\Events\AppActionAckEvent;
use App\Events\AppActionFinEvent;
use Symfony\Component\Console\Input\InputArgument;
use ReflectionClass;
use Exception;

class RedisSubscribe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:subscribe {role}';

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
        	$role = $this->input->getArgument('role');
		if ($role === "manager") {
			Redis::psubscribe(['dm.response.*'], function($message, $channel) {
				$chan = explode('.', $channel);
				$evtType = ucfirst($chan[5]);
				$data = json_decode($message, true)['data'];
				$event = new AppActionFinEvent($data, $message);
			    echo "Manager Subscription got message: $message\n";
				try {
					Event::fire($event);
				} catch (Exception $e) {
					printf("%s\n%s\n", $e->getMessage(), $e->getTraceAsString());
				}
			});
		} else if ($role === "simulator") {
			Redis::psubscribe(['dm.request.*'], function ($message, $channel) {
				$chan = explode('.', $channel);
				$evtType = ucfirst($chan[5]);
				$data = json_decode($message, true)['data'];
				$event = new AppActionAckEvent($data);
				echo "Simulator Subscription got message: $message\n";
				try {
					Event::fire($event);
				} catch (Exception $e) {
					printf("%s\n%s\n", $e->getMessage(), $e->getTraceAsString());
				}
			});
		} else if ($role === "meter") {
			Redis::psubscribe(['dm.response.*'], function($message, $channel) {
				$chan = explode('.', $channel);
				$evtType = ucfirst($chan[5]);
				$data = json_decode($message, true)['data'];
			    echo "Meter Subscription got message: $message\n";
				try {
					//send kill signal to meter service.
					$connection = Redis::connection('pubsub');
        			$meterpid = $connection->get(MeterServiceCommand::METER_PID_KEY);
                    $connection->rpush('process:'.$meterpid.':queue', json_encode($message));
					echo "Sending SIGUSR2 to $meterpid\n";
                    posix_kill($meterpid, SIGUSR2);
				} catch (Exception $e) {
					printf("%s\n%s\n", $e->getMessage(), $e->getTraceAsString());
				}
			});
		} else {
			printf("Invalid role specified.\n");
		}
	} catch (Exception $e) {
	    	printf("%s\n%s\n", $e->getMessage(), $e->getTraceAsString());
	}
    }

    protected function getArguments()
    {
        return [
            ['role', InputArgument::REQUIRED, 'The role. May be "manager" or "simulator" (without the quotes).'],
        ];
    }
}

