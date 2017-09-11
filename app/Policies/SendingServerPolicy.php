<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class SendingServerPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function read(\Acelle\Model\User $user, \Acelle\Model\SendingServer $item, $role)
    {
        switch ($role) {
            case 'admin':
                $can = $user->admin->getPermission('sending_server_read') != 'no';
                break;
            case 'customer':
                $can = $user->customer->getOption('sending_server_option') == \Acelle\Model\Plan::SENDING_SERVER_OPTION_OWN;
                break;
        }

        return $can;
    }

    public function readAll(\Acelle\Model\User $user, \Acelle\Model\SendingServer $item, $role)
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

    public function create(\Acelle\Model\User $user, \Acelle\Model\SendingServer $item, $role)
    {
        switch ($role) {
            case 'admin':
                $can = $user->admin->getPermission('sending_server_create') == 'yes';
                break;
            case 'customer':
                $max = $user->customer->getOption('sending_servers_max');
                $can = $user->customer->getOption('sending_server_option') == \Acelle\Model\Plan::SENDING_SERVER_OPTION_OWN
                    && ($user->customer->sendingServersCount() < $max || $max == -1);
                $can = $can && (!isset($item->type) || $user->customer->isAllowCreateSendingServerType($item->type));
                break;
        }

        return $can;
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\SendingServer $item, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('sending_server_update');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $item->admin_id);
                break;
            case 'customer':
                $can = $user->customer->getOption('sending_server_option') == \Acelle\Model\Plan::SENDING_SERVER_OPTION_OWN
                        && $user->customer->id == $item->customer_id;
                break;
        }

        return $can;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\SendingServer $item, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('sending_server_delete');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $item->admin_id);
                break;
            case 'customer':
                $can = $user->customer->getOption('sending_server_option') == \Acelle\Model\Plan::SENDING_SERVER_OPTION_OWN
                    && $user->customer->id == $item->customer_id;
                break;
        }

        return $can;
    }

    public function disable(\Acelle\Model\User $user, \Acelle\Model\SendingServer $item, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('sending_server_update');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $item->admin_id);
                break;
            case 'customer':
                $can = $user->customer->getOption('sending_server_option') == \Acelle\Model\Plan::SENDING_SERVER_OPTION_OWN
                    && $user->customer->id == $item->customer_id;
                break;
        }

        return $can && $item->status != "inactive";
    }

    public function enable(\Acelle\Model\User $user, \Acelle\Model\SendingServer $item, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('sending_server_update');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $item->admin_id);
                break;
            case 'customer':
                $can = $user->customer->getOption('sending_server_option') == \Acelle\Model\Plan::SENDING_SERVER_OPTION_OWN
                    && $user->customer->id == $item->customer_id;
                break;
        }

        return $can && $item->status != "active";
    }

    public function test(\Acelle\Model\User $user, \Acelle\Model\SendingServer $item, $role)
    {
        return $this->update($user, $item, $role) || !isset($item->id);
    }
}
