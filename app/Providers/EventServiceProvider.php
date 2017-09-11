<?php

namespace Acelle\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Acelle\Events\CampaignUpdated' => [
            'Acelle\Listeners\CampaignUpdatedListener',
        ],
        'Acelle\Events\MailListUpdated' => [
            'Acelle\Listeners\MailListUpdatedListener',
        ],
        'Acelle\Events\UserUpdated' => [
            'Acelle\Listeners\UserUpdatedListener',
        ],
        'Acelle\Events\AutomationUpdated' => [
            'Acelle\Listeners\AutomationUpdatedListener',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);
    }
}
