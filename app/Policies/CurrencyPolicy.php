<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class CurrencyPolicy
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

    public function read(\Acelle\Model\User $user, \Acelle\Model\Currency $item)
    {
        $can = $user->admin->getPermission('currency_read') != 'no';

        return $can;
    }

    public function readAll(\Acelle\Model\User $user, \Acelle\Model\Currency $item)
    {
        $can = $user->admin->getPermission('currency_read') == 'all';

        return $can;
    }

    public function create(\Acelle\Model\User $user, \Acelle\Model\Currency $item)
    {
        $can = $user->admin->getPermission('currency_create') == 'yes';

        return $can;
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\Currency $item)
    {
        $ability = $user->admin->getPermission('currency_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\Currency $item)
    {
        $ability = $user->admin->getPermission('currency_delete');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can;
    }

    public function disable(\Acelle\Model\User $user, \Acelle\Model\Currency $item)
    {
        $ability = $user->admin->getPermission('currency_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can && $item->status != 'inactive';
    }

    public function enable(\Acelle\Model\User $user, \Acelle\Model\Currency $item)
    {
        $ability = $user->admin->getPermission('currency_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can && $item->status != 'active';
    }
}
