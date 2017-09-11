<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
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

    public function read(\Acelle\Model\User $user, \Acelle\Model\Customer $item)
    {
        $can = $user->admin->getPermission('customer_read') != 'no';

        return $can;
    }

    public function readAll(\Acelle\Model\User $user, \Acelle\Model\Customer $item)
    {
        $can = $user->admin->getPermission('customer_read') == 'all';

        return $can;
    }

    public function create(\Acelle\Model\User $user, \Acelle\Model\Customer $item)
    {
        $can = $user->admin->getPermission('customer_create') == 'yes';

        return $can;
    }

    public function profile(\Acelle\Model\User $user, \Acelle\Model\Customer $item)
    {
        return $user->id == $item->user_id;
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\Customer $item)
    {
        $ability = $user->admin->getPermission('customer_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\Customer $item)
    {
        $ability = $user->admin->getPermission('customer_delete');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can;
    }

    public function loginAs(\Acelle\Model\User $user, \Acelle\Model\Customer $item)
    {
        $ability = $user->admin->getPermission('customer_login_as');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can && $user->admin->user_id != $item->user_id;
    }

    public function disable(\Acelle\Model\User $user, \Acelle\Model\Customer $item)
    {
        $ability = $user->admin->getPermission('customer_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can && $item->status != 'inactive';
    }

    public function enable(\Acelle\Model\User $user, \Acelle\Model\Customer $item)
    {
        $ability = $user->admin->getPermission('customer_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can && $item->status != 'active';
    }

    public function register(\Acelle\Model\User $user, \Acelle\Model\Customer $item)
    {
        $ability = \Acelle\Model\Setting::get('enable_user_registration') == 'yes';
        $can = $ability;

        return true;
    }

    public function viewSubAccount(\Acelle\Model\User $user, \Acelle\Model\Customer $item)
    {
        $ability = $user->admin->getPermission('customer_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);
        $can = $can && $item->subAccounts()->count();
        return $can;
    }
}
