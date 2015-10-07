<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Appliance;
use App\LoadCurve;
use App\ActionRequest;

class StartAppEvent extends AppActionEvent
{
    use SerializesModels;

    const EVT_TYPE = 'start';

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ActionRequest $actReq)
    {
	parent::__construct($actReq, self::EVT_TYPE);
    }
}
