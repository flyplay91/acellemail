<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
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

    public function read(\Acelle\Model\User $user, \Acelle\Model\Admin $item)
    {
        $can = $user->admin->getPermission('admin_read') != 'no';

        return $can;
    }

    public function readAll(\Acelle\Model\User $user, \Acelle\Model\Admin $item)
    {
        $can = $user->admin->getPermission('admin_read') == 'all';

        return $can;
    }

    public function create(\Acelle\Model\User $user, \Acelle\Model\Admin $item)
    {
        $can = $user->admin->getPermission('admin_create') == 'yes';

        return $can;
    }

    public function profile(\Acelle\Model\User $user, \Acelle\Model\Admin $item)
    {
        return $user->id == $item->user_id;
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\Admin $item)
    {
        $ability = $user->admin->getPermission('admin_update');
        $can = $ability == 'all'
            || ($ability == 'own' && $user->id == $item->creator_id);

        return $can;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\Admin $item)
    {
        $ability = $user->admin->getPermission('admin_delete');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->id == $item->creator_id);

        return $can && $item->customers()->count() == 0;
    }

    public function loginAs(\Acelle\Model\User $user, \Acelle\Model\Admin $item)
    {
        $ability = $user->admin->getPermission('admin_login_as');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->id == $item->creator_id);

        return $can && $user->admin->id != $item->id;
    }

    public function disable(\Acelle\Model\User $user, \Acelle\Model\Admin $item)
    {
        $ability = $user->admin->getPermission('admin_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->id == $item->creator_id);

        return $can && $item->status != 'inactive';
    }

    public function enable(\Acelle\Model\User $user, \Acelle\Model\Admin $item)
    {
        $ability = $user->admin->getPermission('admin_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->id == $item->creator_id);

        return $can && $item->status != 'active';
    }
}
