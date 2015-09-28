<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
	/* Start Events */
	'App\Events\AppStartAckEvent' => [
	    'App\Listeners\AppStartAckListener',
	],
	'App\Events\AppStartFinEvent' => [
	    'App\Listeners\AppStartFinListener',
	],
	/* Stop Events */
	'App\Events\AppStopAckEvent' => [
	    'App\Listeners\AppStopAckListener',
	],
	'App\Events\AppStopFinEvent' => [
	    'App\Listeners\AppStopFinListener',
	],
	/* Pause Events */
	'App\Events\AppPauseAckEvent' => [
	    'App\Listeners\AppPauseAckListener',
	],
	'App\Events\AppPauseFinEvent' => [
	    'App\Listeners\AppPauseFinListener',
	],
	/* Resume Events */
	'App\Events\AppResumeAckEvent' => [
	    'App\Listeners\AppResumeAckListener',
	],
	'App\Events\AppResumeFinEvent' => [
	    'App\Listeners\AppResumeFinListener',
	],
	/* Wake Events */
	'App\Events\AppWakeAckEvent' => [
	    'App\Listeners\AppWakeAckListener',
	],
	'App\Events\AppWakeFinEvent' => [
	    'App\Listeners\AppWakeFinListener',
	],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
