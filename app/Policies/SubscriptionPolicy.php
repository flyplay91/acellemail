<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function readAll(\Acelle\Model\User $user, \Acelle\Model\Subscription $subscription, $role)
    {
        switch ($role) {
            case 'admin':
                $can = $user->admin->getPermission('subscription_read') == 'all';
                break;
            case 'customer':
                $can = false;
                break;
        }

        return $can;
    }

    public function read(\Acelle\Model\User $user, \Acelle\Model\Subscription $subscription, $role)
    {
        switch ($role) {
            case 'admin':
                $can = $user->admin->getPermission('subscription_read') != 'no';
                break;
            case 'customer':
                $can = !$subscription->id || $user->customer->id == $subscription->customer_id;
                break;
        }

        return $can;
    }

    public function create(\Acelle\Model\User $user, \Acelle\Model\Subscription $item, $role)
    {
        switch ($role) {
            case 'admin':
                $can = $user->admin->getPermission('subscription_create') == 'yes';
                break;
            case 'customer':
                $current_subscription = $user->customer->getCurrentSubscription();
                $can = ($current_subscription && !$user->customer->getNextSubscription()) ||
                    $user->customer->getNotOutdatedSubscriptions()->count() == 0;
                $can = $can && (!$current_subscription || !$current_subscription->isTimeUnlimited());
                break;
        }

        return $can;
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\Subscription $subscription, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('subscription_update');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $subscription->customer->admin_id);
                break;
            case 'customer':
                $can = false;
                break;
        }

        return $can;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\Subscription $subscription, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('subscription_delete');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $subscription->customer->admin_id);
                break;
            case 'customer':
                $can = $user->customer->id == $subscription->customer_id &&
                    $subscription->status == \Acelle\Model\Subscription::STATUS_INACTIVE;
                break;
        }

        return $can;
    }

    public function disable(\Acelle\Model\User $user, \Acelle\Model\Subscription $subscription, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('subscription_disable');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $subscription->customer->admin_id);
                break;
            case 'customer':
                $can = $user->customer->id == $subscription->customer_id;
                break;
        }

        return $can && $subscription->status == \Acelle\Model\Subscription::STATUS_ACTIVE && !$subscription->isOld();
    }

    public function enable(\Acelle\Model\User $user, \Acelle\Model\Subscription $subscription, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('subscription_enable');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $subscription->customer->admin_id);
                break;
            case 'customer':
                $can = false;
                break;
        }

        return $can && in_array($subscription->status, [\Acelle\Model\Subscription::STATUS_INACTIVE, \Acelle\Model\Subscription::STATUS_DISABLED]) && !$subscription->isOld();
    }

    public function pay(\Acelle\Model\User $user, \Acelle\Model\Subscription $subscription, $role)
    {
        switch ($role) {
            case 'admin':
                $can = false;
                break;
            case 'customer':
                $can = !$subscription->isPaid();
                break;
        }

        return $can && $subscription->paid == false && !$subscription->isOld();
    }

    public function paid(\Acelle\Model\User $user, \Acelle\Model\Subscription $subscription, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('subscription_paid');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $subscription->customer->admin_id);
                break;
            case 'customer':
                $can = $user->customer->id == $subscription->customer_id;
                break;
        }

        return $can && $subscription->paid == false && !$subscription->isOld();
    }

    public function unpaid(\Acelle\Model\User $user, \Acelle\Model\Subscription $subscription, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('subscription_unpaid');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $subscription->customer->admin_id);
                break;
            case 'customer':
                $can = $user->customer->id == $subscription->customer_id;
                break;
        }

        return $can && $subscription->paid == true && !$subscription->isOld();
    }
}
