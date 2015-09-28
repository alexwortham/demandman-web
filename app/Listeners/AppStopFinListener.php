<?php

namespace App\Listeners;

use App\Events\AppStopFinEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AppStopFinListener
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
     * @param  AppStopFinEvent  $event
     * @return void
     */
    public function handle(AppStopFinEvent $event)
    {
        //
    }
}
