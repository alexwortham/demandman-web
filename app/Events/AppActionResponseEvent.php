<?php

namespace App\Events;

use App\Events\Event;
use App\ActionResponse;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use App\Appliance;

class AppActionResponseEvent extends Event implements ShouldBroadcastNow
{
    use SerializesModels;

    public $actionResponse;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ActionResponse $actRes)
    {
	$this->actionResponse = $actRes;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [sprintf("dm.response.appliance.%d.action.%s", $this->actionResponse->applianceId(),
		$this->actionResponse->getAction())];
    }
}
