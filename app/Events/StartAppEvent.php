<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Appliance;
use App\LoadCurve;

class StartAppEvent extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $appliance;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Appliance $appliance)
    {
        $this->appliance = $appliance;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['dm.start-app'];
    }
}
