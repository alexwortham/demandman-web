<?php

namespace App\Listeners;

use App\Events\AppPauseFinEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AppPauseFinListener
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
     * @param  AppPauseFinEvent  $event
     * @return void
     */
    public function handle(AppPauseFinEvent $event)
    {
        //
    }
}
