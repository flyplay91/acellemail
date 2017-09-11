<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerGroupPolicy
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
    
    public function read(\Acelle\Model\User $user, \Acelle\Model\CustomerGroup $item)
    {
        $can = $user->admin->getPermission('customer_group_read') != 'no';

        return $can;
    }
    
    public function read_all(\Acelle\Model\User $user, \Acelle\Model\CustomerGroup $item)
    {
        $can = $user->admin->getPermission('customer_group_read') == 'all';

        return $can;
    }
    
    public function create(\Acelle\Model\User $user, \Acelle\Model\CustomerGroup $item)
    {
        $can = $user->admin->getPermission('customer_group_create') == 'yes';

        return $can;
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\CustomerGroup $item)
    {
        $ability = $user->admin->getPermission('customer_group_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can;
    }

    public function sort(\Acelle\Model\User $user, \Acelle\Model\CustomerGroup $item)
    {
        $ability = $user->admin->getPermission('customer_group_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\CustomerGroup $item)
    {
        $ability = $user->admin->getPermission('customer_group_delete');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $item->admin_id);

        return $can;
    }
}
