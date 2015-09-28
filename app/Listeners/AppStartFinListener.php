<?php

namespace App\Listeners;

use App\Events\AppStartFinEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AppStartFinListener
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
     * @param  AppStartFinEvent  $event
     * @return void
     */
    public function handle(AppStartFinEvent $event)
    {
        //
    }
}
