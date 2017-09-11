<?php

namespace Acelle\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'Acelle\Model' => 'Acelle\Policies\ModelPolicy',
        \Acelle\Model\User::class => \Acelle\Policies\UserPolicy::class,
        \Acelle\Model\Contact::class => \Acelle\Policies\ContactPolicy::class,
        \Acelle\Model\MailList::class => \Acelle\Policies\MailListPolicy::class,
        \Acelle\Model\Subscriber::class => \Acelle\Policies\SubscriberPolicy::class,
        \Acelle\Model\Segment::class => \Acelle\Policies\SegmentPolicy::class,
        \Acelle\Model\Layout::class => \Acelle\Policies\LayoutPolicy::class,
        \Acelle\Model\Template::class => \Acelle\Policies\TemplatePolicy::class,
        \Acelle\Model\Campaign::class => \Acelle\Policies\CampaignPolicy::class,
        \Acelle\Model\SendingServer::class => \Acelle\Policies\SendingServerPolicy::class,
        \Acelle\Model\BounceHandler::class => \Acelle\Policies\BounceHandlerPolicy::class,
        \Acelle\Model\FeedbackLoopHandler::class => \Acelle\Policies\FeedbackLoopHandlerPolicy::class,
        \Acelle\Model\SendingDomain::class => \Acelle\Policies\SendingDomainPolicy::class,
        \Acelle\Model\Language::class => \Acelle\Policies\LanguagePolicy::class,
        \Acelle\Model\CustomerGroup::class => \Acelle\Policies\CustomerGroupPolicy::class,
        \Acelle\Model\Customer::class => \Acelle\Policies\CustomerPolicy::class,
        \Acelle\Model\AdminGroup::class => \Acelle\Policies\AdminGroupPolicy::class,
        \Acelle\Model\Admin::class => \Acelle\Policies\AdminPolicy::class,
        \Acelle\Model\Setting::class => \Acelle\Policies\SettingPolicy::class,
        \Acelle\Model\Plan::class => \Acelle\Policies\PlanPolicy::class,
        \Acelle\Model\Currency::class => \Acelle\Policies\CurrencyPolicy::class,
        \Acelle\Model\SystemJob::class => \Acelle\Policies\SystemJobPolicy::class,
        \Acelle\Model\Automation::class => \Acelle\Policies\AutomationPolicy::class,
        \Acelle\Model\AutoEvent::class => \Acelle\Policies\AutoEventPolicy::class,
        \Acelle\Model\Subscription::class => \Acelle\Policies\SubscriptionPolicy::class,
        \Acelle\Model\PaymentMethod::class => \Acelle\Policies\PaymentMethodPolicy::class,
        \Acelle\Model\EmailVerificationServer::class => \Acelle\Policies\EmailVerificationServerPolicy::class,
        \Acelle\Model\Blacklist::class => \Acelle\Policies\BlacklistPolicy::class,
        \Acelle\Model\SubAccount::class => \Acelle\Policies\SubAccountPolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param \Illuminate\Contracts\Auth\Access\Gate $gate
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);
    }
}
