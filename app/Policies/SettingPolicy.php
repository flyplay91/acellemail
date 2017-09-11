<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class SettingPolicy
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
    
    public function general(\Acelle\Model\User $user, \Acelle\Model\Setting $item)
    {
        $can = $user->admin->getPermission('setting_general') == 'yes';

        return $can;
    }
    
    public function sending(\Acelle\Model\User $user, \Acelle\Model\Setting $item)
    {
        $can = $user->admin->getPermission('setting_sending') == 'yes';

        return $can;
    }
    
    public function system_urls(\Acelle\Model\User $user, \Acelle\Model\Setting $item)
    {
        $can = $user->admin->getPermission('setting_system_urls') == 'yes';

        return $can;
    }
    
    public function access_when_offline(\Acelle\Model\User $user, \Acelle\Model\Setting $item)
    {
        $can = $user->admin->getPermission('setting_access_when_offline') == 'yes';

        return $can;
    }
}
