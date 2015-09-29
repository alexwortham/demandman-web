<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Appliance;
use App\LoadCurve;

class PauseAppEvent extends AppActionEvent
{
    use SerializesModels;

    const EVT_TYPE = 'pause';

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Appliance $appliance)
    {
	parent::__construct($appliance, self::EVT_TYPE);
    }
}
