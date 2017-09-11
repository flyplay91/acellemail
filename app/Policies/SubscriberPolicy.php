<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriberPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function read(\Acelle\Model\User $user, \Acelle\Model\Subscriber $item)
    {
        $customer = $user->customer;
        return $item->mailList->customer_id == $customer->id;
    }

    public function create(\Acelle\Model\User $user, \Acelle\Model\Subscriber $item)
    {
        $customer = $user->customer;
        $max = $customer->getOption('subscriber_max');
        $max_per_list = $customer->getOption('subscriber_per_list_max');

        return $customer->id == $item->mailList->customer_id &&
            ($max > $customer->subscribersCount() || $max == -1) &&
            ($max_per_list > $item->mailList->subscribersCount() || $max_per_list == -1);
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\Subscriber $item)
    {
        $customer = $user->customer;
        return $item->mailList->customer_id == $customer->id;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\Subscriber $item)
    {
        $customer = $user->customer;
        return $item->mailList->customer_id == $customer->id;
    }

    public function subscribe(\Acelle\Model\User $user, \Acelle\Model\Subscriber $item)
    {
        $customer = $user->customer;
        return $item->mailList->customer_id == $customer->id && $item->status == 'unsubscribed';
    }

    public function unsubscribe(\Acelle\Model\User $user, \Acelle\Model\Subscriber $item)
    {
        $customer = $user->customer;
        return $item->mailList->customer_id == $customer->id && $item->status == 'subscribed';
    }
}
