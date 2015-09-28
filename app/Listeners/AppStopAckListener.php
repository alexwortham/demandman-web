<?php

namespace App\Listeners;

use App\Events\AppStopAckEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AppStopAckListener extends AppActionAckListener implements ShouldQueue
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
     * @param  AppStopAckEvent  $event
     * @return void
     */
    public function handle(AppStopAckEvent $event)
    {
        printf("AppStopAckEvent received.\n");
	//Here we check if the request is approved or denied,
	//and start the simulation if necessary.
	parent::handle($event);
    }
}
