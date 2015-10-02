<?php

namespace App\Events;

use App\Events\Event;
use App\AppActionRequest;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use App\Appliance;

class AppActionEvent extends Event implements ShouldBroadcastNow
{
    use SerializesModels;

    public $appliance;
    public $action;
    public $appActionRequest;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Appliance $appliance, AppActionRequest $actReq, $action)
    {
        $this->appliance = $appliance;
	$this->appActionRequest = $actReq;
        $this->action = $action;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['dm.' . $this->appliance->id . ".". $this->action];
    }
}
