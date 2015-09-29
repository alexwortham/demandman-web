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
    public $appActionRequest = NULL;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Appliance $appliance, $action)
    {
        $this->appliance = $appliance;
        $this->action = $action;
    }

    public function makeAppActionRequest() {
	if ($this->appActionRequest === NULL) {
		$this->appActionRequest = new AppActionRequest();
		$this->appActionRequest->appId = $this->appliance->id;
		$this->appActionRequest->action = $this->action;
		$this->appActionRequest->save();
	}
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
