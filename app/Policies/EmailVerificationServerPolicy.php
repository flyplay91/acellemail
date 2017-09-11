<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class EmailVerificationServerPolicy
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

    public function read(\Acelle\Model\User $user, \Acelle\Model\EmailVerificationServer $server, $role)
    {
        switch ($role) {
            case 'admin':
                $can = $user->admin->getPermission('email_verification_server_read') != 'no';
                break;
            case 'customer':
                $can = $user->customer->getOption('create_email_verification_servers') == 'yes';
                break;
        }

        return $can;
    }

    public function readAll(\Acelle\Model\User $user, \Acelle\Model\EmailVerificationServer $server, $role)
    {
        switch ($role) {
            case 'admin':
                $can = $user->admin->getPermission('email_verification_server_read') == 'all';
                break;
            case 'customer':
                $can = false;
                break;
        }

        return $can;
    }

    public function create(\Acelle\Model\User $user, \Acelle\Model\EmailVerificationServer $server, $role)
    {
        switch ($role) {
            case 'admin':
                $can = $user->admin->getPermission('email_verification_server_create') == 'yes';
                break;
            case 'customer':
                $max = $user->customer->getOption('email_verification_servers_max');
                $can = $user->customer->getOption('create_email_verification_servers') == 'yes'
                    && ($user->customer->emailVerificationServersCount() < $max || $max == -1);
                break;
        }

        return $can;
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\EmailVerificationServer $server, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('email_verification_server_update');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $server->admin_id);
                break;
            case 'customer':
                $can = $user->customer->getOption('create_email_verification_servers') == 'yes' && $user->customer->id == $server->customer_id;
                break;
        }

        return $can;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\EmailVerificationServer $server, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('email_verification_server_delete');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $server->admin_id);
                break;
            case 'customer':
                $can = $user->customer->getOption('create_email_verification_servers') == 'yes'
                    && $user->customer->id == $server->customer_id;
                break;
        }

        return $can;
    }

    public function disable(\Acelle\Model\User $user, \Acelle\Model\EmailVerificationServer $server, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('email_verification_server_update');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $server->admin_id);
                break;
            case 'customer':
                $can = $user->customer->getOption('create_email_verification_servers') == 'yes'
                    && $user->customer->id == $server->customer_id;
                break;
        }

        return $can && $server->status != \Acelle\Model\EmailVerificationServer::STATUS_INACTIVE;
    }

    public function enable(\Acelle\Model\User $user, \Acelle\Model\EmailVerificationServer $server, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('email_verification_server_update');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $server->admin_id);
                break;
            case 'customer':
                $can = $user->customer->getOption('create_email_verification_servers') == 'yes'
                    && $user->customer->id == $server->customer_id;
                break;
        }

        return $can && $server->status != \Acelle\Model\EmailVerificationServer::STATUS_ACTIVE;
    }
}
