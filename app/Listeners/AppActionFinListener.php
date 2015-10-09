<?php

namespace App\Listeners;

use App\Events\AppActionEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Redis\Database as RedisDatabase;
use App\Services\ApplianceApi as Api;

class AppActionFinListener
{
	protected $api;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * Handle the event.
     *
     * @param  AppActionEvent  $event
     * @return void
     */
    public function handle($event)
    {
	$responseMessage = $event->message;
	$action = ucfirst($event->data['actionResponse']['action']);
	call_user_func_array(array($this->api, "confirm$action"),
		array($event->data['actionResponse']['requestId'], $responseMessage));
    }
}
