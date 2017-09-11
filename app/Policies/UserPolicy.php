<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }
    
    public function read(\Acelle\Model\User $user, \Acelle\Model\User $item)
    {
        $can = $user->admin->getPermission('user_read') != 'no';

        return $can;
    }
    
    public function read_all(\Acelle\Model\User $user, \Acelle\Model\User $item)
    {
        $can = $user->admin->getPermission('user_read') == 'all';

        return $can;
    }

    public function create(\Acelle\Model\User $user, \Acelle\Model\User $item)
    {
        $can = $user->admin->getPermission('user_create') == 'yes';

        return $can;
    }

    public function profile(\Acelle\Model\User $user, \Acelle\Model\User $item)
    {
        return $user->id == $item->id;
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\User $item)
    {
        $ability = $user->admin->getPermission('user_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->id == $item->user_id);

        return $can;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\User $item)
    {
        $ability = $user->admin->getPermission('user_delete');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->id == $item->user_id);
        $can = $can && $user->id != $item->id;

        return $can;
    }

    public function switch_user(\Acelle\Model\User $user, \Acelle\Model\User $item)
    {
        $ability = $user->admin->getPermission('user_switch');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->id == $item->user_id);
        $can = $can && $item->id != $user->id;

        return $can;
    }
    
    public function customer_access(\Acelle\Model\User $user, \Acelle\Model\User $item)
    {        
        return is_object($user->customer);
    }
    
    public function admin_access(\Acelle\Model\User $user, \Acelle\Model\User $item)
    {        
        return is_object($user->admin);
    }
    
    public function reseller_access(\Acelle\Model\User $user, \Acelle\Model\User $item)
    {        
        return is_object($user->reseller);
    }
    
    public function change_group(\Acelle\Model\User $user, \Acelle\Model\User $item)
    {
        $ability = $user->admin->getPermission('user_update');
        $can = $ability == 'all';

        return $can;
    }
}
