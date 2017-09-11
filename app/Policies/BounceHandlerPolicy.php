<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class BounceHandlerPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function read(\Acelle\Model\User $user, \Acelle\Model\BounceHandler $item)
    {
        $ability = $user->admin->getPermission('bounce_handler_read');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can;
    }

    public function readAll(\Acelle\Model\User $user, \Acelle\Model\BounceHandler $item)
    {
        $can = $user->admin->getPermission('bounce_handler_read') == 'all';

        return $can;
    }

    public function create(\Acelle\Model\User $user, \Acelle\Model\BounceHandler $item)
    {
        $can = $user->admin->getPermission('bounce_handler_create') == 'yes';

        return $can;
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\BounceHandler $item)
    {
        $ability = $user->admin->getPermission('bounce_handler_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\BounceHandler $item)
    {
        $ability = $user->admin->getPermission('bounce_handler_delete');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can;
    }

    public function test(\Acelle\Model\User $user, \Acelle\Model\BounceHandler $item)
    {
        return $this->update($user, $item) || !isset($item->id);
    }
}
