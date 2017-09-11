<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class SubAccountPolicy
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

    public function read(\Acelle\Model\User $user, \Acelle\Model\SubAccount $sub_account, $role)
    {
        switch ($role) {
            case 'admin':
                $can = $user->admin->getPermission('sending_server_read') != 'no';
                break;
            case 'customer':
                $can = false;
                break;
        }

        return $can;
    }

    public function readAll(\Acelle\Model\User $user, \Acelle\Model\SubAccount $sub_account, $role)
    {
        switch ($role) {
            case 'admin':
                $can = $user->admin->getPermission('sending_server_read') == 'all';
                break;
            case 'customer':
                $can = false;
                break;
        }

        return $can;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\SubAccount $sub_account, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('sending_server_delete');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $sub_account->sendingServer->admin_id);
                break;
            case 'customer':
                $can = false;
                break;
        }

        return $can;
    }
}
