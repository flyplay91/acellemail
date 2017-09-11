<?php

namespace Acelle\Listeners;

use Acelle\Events\MailListUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Acelle\Model\SystemJob as SystemJobModel;

class MailListUpdatedListener
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
    public function handle(MailListUpdated $event)
    {
        if ($event->delayed) {
            $existed = SystemJobModel::getNewJobs()
                           ->where('name', \Acelle\Jobs\UpdateMailListJob::class)
                           ->where('data', $event->mailList->id)
                           ->exists();

            if (!$existed) {
                dispatch(new \Acelle\Jobs\UpdateMailListJob($event->mailList));
            }
        } else {
            $event->mailList->updateCachedInfo();
        }
    }
}
