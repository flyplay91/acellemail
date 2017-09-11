<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class ContactPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\Contact $item)
    {
        return !isset($item->id) || $user->contact_id == $item->id;
    }
}
