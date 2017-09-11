<?php

namespace Acelle\Listeners;

use Acelle\Events\CampaignUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Acelle\Jobs\UpdateCampaignJob;
use Acelle\Jobs\UpdateMailListJob;
use Acelle\Model\SystemJob as SystemJobModel;

class CampaignUpdatedListener
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
     * @param  CampaignUpdated  $event
     * @return void
     */
    public function handle(CampaignUpdated $event)
    {
        if ($event->delayed) {
            $existed = SystemJobModel::getNewJobs()
                           ->where('name', UpdateCampaignJob::class)
                           ->where('data', $event->campaign->id)
                           ->exists();

            if (!$existed) {
                $existed = SystemJobModel::getNewJobs()
                           ->where('name', UpdateMailListJob::class)
                           ->whereIn('data', $event->campaign->mailLists->map(function($r) { return $r->id;  })->toArray())
                           ->exists();
            }

            if (!$existed) {
                dispatch(new UpdateCampaignJob($event->campaign));
            }
        } else {
            // @deprecated
            $event->campaign->updateCache();
        }
    }
}
