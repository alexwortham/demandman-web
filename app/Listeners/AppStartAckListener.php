<?php

namespace App\Listeners;

use App\Events\AppStartAckEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AppStartAckListener implements ShouldQueue
{
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
     * @param  AppStartAckEvent  $event
     * @return void
     */
    public function handle(AppStartAckEvent $event)
    {
        echo "I did something naughty";
	//Here we check if the request is approved or denied,
	//and start the simulation if necessary.
    }
}
