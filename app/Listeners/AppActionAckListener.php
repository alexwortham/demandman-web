<?php

namespace App\Listeners;

use App\Events\AppActionEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Redis;

class AppActionAckListener implements ShouldQueue
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
    public function handle(AppActionAckEvent $event)
    {
        $simpid = Redis::get(self::SIM_PID_KEY);
	Redis::rpush('process:'.$simpid.':queue', $event);
	posix_kill($simpid, SIGUSR1);
    }
}
