<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class AutoEventPolicy
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
    
    public function disable(\Acelle\Model\User $user, \Acelle\Model\AutoEvent $event)
    {
        return $event->automation->customer_id == $user->customer->id && in_array($event->status, [
                                                                \Acelle\Model\AutoEvent::STATUS_ACTIVE
                                                            ]);
    }
    
    public function enable(\Acelle\Model\User $user, \Acelle\Model\AutoEvent $event)
    {
        return $event->automation->customer_id == $user->customer->id && in_array($event->status, [
                                                                \Acelle\Model\AutoEvent::STATUS_INACTIVE
                                                            ]);
    }
    
    public function update(\Acelle\Model\User $user, \Acelle\Model\AutoEvent $event)
    {
        return $event->automation->customer_id == $user->customer->id;
    }
    
    public function moveUp(\Acelle\Model\User $user, \Acelle\Model\AutoEvent $event)
    {
        return $event->automation->customer_id == $user->customer->id && $event->previous_event_id != $event->automation->getInitEvent()->id;
    }
    
    public function moveDown(\Acelle\Model\User $user, \Acelle\Model\AutoEvent $event)
    {
        return $event->automation->customer_id == $user->customer->id && isset($event->nextEvent);
    }
}
