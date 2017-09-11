<?php

namespace Acelle\Listeners;

use Acelle\Events\AutomationUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Acelle\Model\SystemJob as SystemJobModel;

class AutomationUpdatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MailListUpdated  $event
     * @return void
     */
    public function handle(AutomationUpdated $event)
    {
        if ($event->delayed) {
            $existed = SystemJobModel::getNewJobs()
                           ->where('name', \Acelle\Jobs\UpdateAutomationJob::class)
                           ->where('data', $event->automation->id)
                           ->exists();

            if (!$existed) {
                dispatch(new \Acelle\Jobs\UpdateAutomationJob($event->automation));
            }
        } else {
            $event->automation->updateCache();
        }
    }
}
