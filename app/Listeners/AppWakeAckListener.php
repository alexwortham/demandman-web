<?php

namespace App\Listeners;

use App\Events\AppWakeAckEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AppWakeAckListener extends AppActionAckListener
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
     * @param  AppWakeAckEvent  $event
     * @return void
     */
    public function handle($event)
    {
        //
        printf("AppWakeAckEvent received.\n");
	//Here we check if the request is approved or denied,
	//and start the simulation if necessary.
	parent::handle($event);
    }
}
