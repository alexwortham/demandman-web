<?php

namespace App\Listeners;

use App\Events\AppActionEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Redis\Database as RedisDatabase;
use Redis;

class AppActionAckListener
{
	const SIM_PID_KEY = 'simpid';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  AppActionEvent  $event
     * @return void
     */
    public function handle($event)
    {
	$connection = Redis::connection('pubsub');
        $simpid = $connection->get(self::SIM_PID_KEY);
	$connection->rpush('process:'.$simpid.':queue', json_encode($event));
	posix_kill($simpid, SIGUSR1);
    }
}
