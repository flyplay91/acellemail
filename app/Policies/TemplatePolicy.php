<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class TemplatePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function read(\Acelle\Model\User $user, \Acelle\Model\Template $item, $role)
    {
        switch ($role) {
            case 'admin':
                $can = $user->admin->getPermission('template_read') != 'no';
                break;
            case 'customer':
                $can = $user->customer->id == $item->customer_id || !isset($item->customer_id);
                break;
        }

        return $can;
    }

    public function readAll(\Acelle\Model\User $user, \Acelle\Model\Template $item, $role)
    {
        switch ($role) {
            case 'admin':
                $can = $user->admin->getPermission('template_read') == 'all';
                break;
            case 'customer':
                $can = false;
                break;
        }

        return $can;
    }

    public function create(\Acelle\Model\User $user, \Acelle\Model\Template $item, $role)
    {
        switch ($role) {
            case 'admin':
                $can = $user->admin->getPermission('template_create') == 'yes';
                break;
            case 'customer':
                $can = true;
                break;
        }

        return $can;
    }

    public function view(\Acelle\Model\User $user, \Acelle\Model\Template $item, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('template_read');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $item->admin_id);
                break;
            case 'customer':
                $can = $user->customer->id == $item->customer_id || !isset($item->customer_id);
                break;
        }

        return $can;
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\Template $item, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('template_update');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $item->admin_id);
                break;
            case 'customer':
                $can = $user->customer->id == $item->customer_id;
                break;
        }

        return $can;
    }

    public function image(\Acelle\Model\User $user, \Acelle\Model\Template $item, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('template_read');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $item->admin_id);
                break;
            case 'customer':
                $can = $user->customer->id == $item->customer_id || !isset($item->customer_id);
                break;
        }

        return $can;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\Template $item, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('template_delete');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $item->admin_id);
                break;
            case 'customer':
                $can = $user->customer->id == $item->customer_id;
                break;
        }

        return $can;
    }

    public function preview(\Acelle\Model\User $user, \Acelle\Model\Template $item, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('template_read');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $item->admin_id);
                break;
            case 'customer':
                $can = $user->customer->id == $item->customer_id || !isset($item->customer_id);
                break;
        }

        return $can;
    }

    public function saveImage(\Acelle\Model\User $user, \Acelle\Model\Template $item, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('template_update');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $item->admin_id);
                break;
            case 'customer':
                $can = $user->customer->id == $item->customer_id || !isset($item->customer_id);
                break;
        }

        return $can;
    }

    public function copy(\Acelle\Model\User $user, \Acelle\Model\Template $item, $role)
    {
        switch ($role) {
            case 'admin':
                $ability = $user->admin->getPermission('template_update');
                $can = $ability == 'all'
                    || ($ability == 'own' && $user->admin->id == $item->admin_id);
                break;
            case 'customer':
                $can = $user->customer->id == $item->customer_id || !isset($item->customer_id);
                break;
        }

        return $can;
    }
}
