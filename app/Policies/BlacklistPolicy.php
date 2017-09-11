<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class BlacklistPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function read(\Acelle\Model\User $user, \Acelle\Model\Blacklist $blacklist, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('report_blacklist');
                $can = $ability == 'yes';
                break;
            case 'customer':
                $can = true;
                break;
        }

        return $can;
    }

    public function readAll(\Acelle\Model\User $user, \Acelle\Model\Blacklist $blacklist, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('report_blacklist');
                $can = $ability == 'yes';
                break;
            case 'customer':
                $can = false;
                break;
        }

        return $can;
    }

    public function create(\Acelle\Model\User $user, \Acelle\Model\Blacklist $blacklist, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('report_blacklist');
                $can = $ability == 'yes';
                break;
            case 'customer':
                $can = true;
                break;
        }

        return $can;
    }

    public function import(\Acelle\Model\User $user, \Acelle\Model\Blacklist $blacklist, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('report_blacklist');
                $can = $ability == 'yes' && !$user->admin->getActiveImportBlacklistJobs()->count();
                break;
            case 'customer':
                $can = $user->customer->getActiveImportBlacklistJobs()->count() == 0;
                break;
        }

        return $can;
    }

    public function importCancel(\Acelle\Model\User $user, \Acelle\Model\Blacklist $blacklist, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('report_blacklist');
                $can = $ability == 'yes';
                break;
            case 'customer':
                $can = $user->customer->getActiveImportBlacklistJobs()->count();
                break;
        }

        return $can;
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\Blacklist $blacklist, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('report_blacklist');
                $can = $ability == 'yes';
                break;
            case 'customer':
                $can = $user->customer->id == $blacklist->customer_id;
                break;
        }

        return $can;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\Blacklist $blacklist, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('report_blacklist');
                $can = $ability == 'yes';
                break;
            case 'customer':
                $can = $user->customer->id == $blacklist->customer_id;
                break;
        }

        return $can;
    }
}
