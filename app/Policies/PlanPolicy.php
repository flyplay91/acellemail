<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class PlanPolicy
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

    public function read(\Acelle\Model\User $user, \Acelle\Model\Plan $item)
    {
        $can = $user->admin->getPermission('plan_read') != 'no';

        return $can;
    }

    public function readAll(\Acelle\Model\User $user, \Acelle\Model\Plan $item)
    {
        $can = $user->admin->getPermission('plan_read') == 'all';

        return $can;
    }

    public function create(\Acelle\Model\User $user, \Acelle\Model\Plan $item)
    {
        $can = $user->admin->getPermission('plan_create') == 'yes';

        return $can;
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\Plan $item)
    {
        $ability = $user->admin->getPermission('plan_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\Plan $item)
    {
        $ability = $user->admin->getPermission('plan_delete');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can && !$item->isDefault();
    }

    public function disable(\Acelle\Model\User $user, \Acelle\Model\Plan $item)
    {
        $ability = $user->admin->getPermission('plan_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can && $item->status != 'inactive' && !$item->isDefault();
    }

    public function enable(\Acelle\Model\User $user, \Acelle\Model\Plan $item)
    {
        $ability = $user->admin->getPermission('plan_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can && $item->status != 'active' && !$item->isDefault();
    }
}
