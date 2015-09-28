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
	printf("%d: Building new AppStartAckListener.\n", posix_getpid());
    }

    /**
     * Handle the event.
     *
     * @param  AppStartAckEvent  $event
     * @return void
     */
    public function handle(AppStartAckEvent $event)
    {
        printf("%d: I did something naughty\n", posix_getpid());
	//Here we check if the request is approved or denied,
	//and start the simulation if necessary.
    }
}
