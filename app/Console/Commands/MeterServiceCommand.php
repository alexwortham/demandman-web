<?php

namespace App\Console\Commands;

use Redis;
use Illuminate\Console\Command;
use Event;
use App\Events\AppActionAckEvent;
use App\Events\AppActionFinEvent;
use Symfony\Component\Console\Input\InputArgument;
use App\Services\Meter;
use ReflectionClass;
use Exception;
use \ErrorException;

if (function_exists('pcntl_signal')) {
	pcntl_signal(SIGUSR2, function ($signo) {
		$connection = Redis::connection('pubsub');
        $event = $connection->lpop(MeterServiceCommand::$eventKey);
        MeterServiceCommand::$meter->setEvent($event);
	});
}

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

    public static $meter;
    public static $event = NULL;
    public static $pid = NULL;
    public static $eventKey = NULL;
    const METER_PID_KEY = 'meterpid';

    /**
     * Create a new command instance.
     * @param Meter $meter
     */
    public function __construct(Meter $meter)
    {
        parent::__construct();
	    self::$meter = $meter;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        self::$pid = posix_getpid();
        self::$eventKey = 'process:'.self::$pid.':queue';
        $connection = Redis::connection('pubsub');
        $connection->set(self::METER_PID_KEY, self::$pid);

        try {
            self::$meter->meterLoop();
        } catch (ErrorException $e) {
            printf("%s\n%s\n", $e->getMessage(), $e->getTraceAsString());
        }
    }


}

