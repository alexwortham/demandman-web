<?php

namespace App\Listeners;

use App\Events\AppResumeFinEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AppResumeFinListener
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
     * @param  AppResumeFinEvent  $event
     * @return void
     */
    public function handle(AppResumeFinEvent $event)
    {
        //
    }
}
