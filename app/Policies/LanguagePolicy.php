<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class LanguagePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }
    
    public function read(\Acelle\Model\User $user, \Acelle\Model\Language $item)
    {
        $can = $user->admin->getPermission('language_read') != 'no';

        return $can;
    }
    
    public function read_all(\Acelle\Model\User $user, \Acelle\Model\Language $item)
    {
        $can = $user->admin->getPermission('language_read') == 'all';

        return $can;
    }

    public function create(\Acelle\Model\User $user, \Acelle\Model\Language $item)
    {
        $can = $user->admin->getPermission('language_create') == 'yes';

        return $can;
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\Language $item)
    {
        $ability = $user->admin->getPermission('language_update');
        $can = $ability == 'yes' && !$item->is_default;

        return $can;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\Language $item)
    {
        $ability = $user->admin->getPermission('language_delete');
        $can = $ability == 'yes' && !$item->is_default;

        return $can;
    }
    
    public function translate(\Acelle\Model\User $user, \Acelle\Model\Language $item)
    {
        $ability = $user->admin->getPermission('language_update');
        $can = $ability == 'yes';

        return $can;
    }
    
    public function disable(\Acelle\Model\User $user, \Acelle\Model\Language $item)
    {
        $ability = $user->admin->getPermission('language_update');
        $can = $ability == 'yes' && !$item->is_default;

        return ($can && $item->status != "inactive");
    }
    
    public function enable(\Acelle\Model\User $user, \Acelle\Model\Language $item)
    {
        $ability = $user->admin->getPermission('language_update');
        $can = $ability == 'yes' && !$item->is_default;

        return ($can && $item->status != "active");
    }
    
    public function download(\Acelle\Model\User $user, \Acelle\Model\Language $item)
    {
        $ability = $user->admin->getPermission('language_read');
        $can = $ability == 'yes';

        return $can;
    }
    
    public function upload(\Acelle\Model\User $user, \Acelle\Model\Language $item)
    {
        $ability = $user->admin->getPermission('language_update');
        $can = $ability == 'yes';

        return $can;
    }
}
