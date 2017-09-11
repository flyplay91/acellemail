<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class AutomationPolicy
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
    
    public function read(\Acelle\Model\User $user, \Acelle\Model\Automation $automation)
    {
        return $automation->customer_id == $user->customer->id;
    }

    public function create(\Acelle\Model\User $user, \Acelle\Model\Automation $automation)
    {
        $customer = $user->customer;
        $max = $customer->getOption('automation_max');

        return $max > $customer->automationsCount() || $max == -1;
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\Automation $automation)
    {
        return !isset($automation->id) || $automation->customer_id == $user->customer->id && in_array($automation->status, [
                                                                \Acelle\Model\Automation::STATUS_DRAFT,
                                                                \Acelle\Model\Automation::STATUS_ACTIVE,
                                                                \Acelle\Model\Automation::STATUS_INACTIVE
                                                            ]);
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\Automation $automation)
    {
        return $automation->customer_id == $user->customer->id && in_array($automation->status, [
                                                                \Acelle\Model\Automation::STATUS_DRAFT,
                                                                \Acelle\Model\Automation::STATUS_ACTIVE,
                                                                \Acelle\Model\Automation::STATUS_INACTIVE
                                                            ]);
    }
    
    public function sort(\Acelle\Model\User $user, \Acelle\Model\Automation $automation)
    {
        return $automation->customer_id == $user->customer->id;
    }
    
    public function confirm(\Acelle\Model\User $user, \Acelle\Model\Automation $automation)
    {
        return $automation->customer_id == $user->customer->id && $automation->isValid();
    }
    
    public function overview(\Acelle\Model\User $user, \Acelle\Model\Automation $automation)
    {
        return $automation->customer_id == $user->customer->id && $automation->isValid();
    }
    
    public function enable(\Acelle\Model\User $user, \Acelle\Model\Automation $automation)
    {
        return $automation->customer_id == $user->customer->id && in_array($automation->status, [
                                                                \Acelle\Model\Automation::STATUS_INACTIVE
                                                            ]);
    }
    
    public function disable(\Acelle\Model\User $user, \Acelle\Model\Automation $automation)
    {
        return $automation->customer_id == $user->customer->id && in_array($automation->status, [
                                                                \Acelle\Model\Automation::STATUS_ACTIVE
                                                            ]);
    }
}
