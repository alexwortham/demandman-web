<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Appliance;
use App\LoadCurve;
use App\AppActionRequest;

class ResumeAppEvent extends AppActionEvent
{
    use SerializesModels;

    const EVT_TYPE = 'resume';

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Appliance $appliance, AppActionRequest $actReq)
    {
	parent::__construct($appliance, $actReq, self::EVT_TYPE);
    }
}
