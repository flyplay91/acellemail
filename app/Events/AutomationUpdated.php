<?php

namespace Acelle\Events;

use Acelle\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AutomationUpdated extends Event
{
    use SerializesModels;

    public $automation;
    public $delayed;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($automation, $delayed = true)
    {
        $this->automation = $automation;
        $this->delayed = $delayed;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
