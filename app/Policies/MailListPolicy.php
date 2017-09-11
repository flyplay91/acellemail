<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class MailListPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function read(\Acelle\Model\User $user, \Acelle\Model\MailList $item)
    {
        $customer = $user->customer;
        return $item->customer_id == $customer->id;
    }

    public function create(\Acelle\Model\User $user)
    {
        $customer = $user->customer;
        $max = $customer->getOption('list_max');

        return $max > $customer->lists()->count() || $max == -1;
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\MailList $item)
    {
        $customer = $user->customer;
        return $item->customer_id == $customer->id;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\MailList $item)
    {
        $customer = $user->customer;
        return $item->customer_id == $customer->id;
    }

    public function addMoreSubscribers(\Acelle\Model\User $user, \Acelle\Model\MailList $mailList, $numberOfSubscribers)
    {
        $max = $user->customer->getOption('subscriber_max');
        $maxPerList = $user->customer->getOption('subscriber_per_list_max');
        return $user->customer->id == $mailList->customer_id &&
            ($max >= $user->customer->subscribersCount() + $numberOfSubscribers || $max == -1) &&
            ($maxPerList >= $mailList->subscribersCount() + $numberOfSubscribers || $maxPerList == -1);
    }

    public function import(\Acelle\Model\User $user, \Acelle\Model\MailList $item)
    {
        $customer = $user->customer;
        $can = $customer->getOption('list_import');

        return ($can == 'yes' && $item->customer_id == $customer->id);
    }

    public function export(\Acelle\Model\User $user, \Acelle\Model\MailList $item)
    {
        $customer = $user->customer;
        $can = $customer->getOption('list_export');

        return ($can == 'yes' && $item->customer_id == $customer->id);
    }
}
