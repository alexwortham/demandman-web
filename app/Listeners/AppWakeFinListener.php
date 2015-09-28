<?php

namespace App\Listeners;

use App\Events\AppWakeFinEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AppWakeFinListener
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
     * @param  AppWakeFinEvent  $event
     * @return void
     */
    public function handle(AppWakeFinEvent $event)
    {
        //
    }
}
