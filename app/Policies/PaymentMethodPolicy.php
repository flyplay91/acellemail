<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentMethodPolicy
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

    public function read(\Acelle\Model\User $user, \Acelle\Model\PaymentMethod $payment_method)
    {
        $can = $user->admin->getPermission('payment_method_read') != 'no';

        return $can;
    }

    public function readAll(\Acelle\Model\User $user, \Acelle\Model\PaymentMethod $item)
    {
        $can = $user->admin->getPermission('payment_method_read') == 'all';

        return $can;
    }

    public function create(\Acelle\Model\User $user, \Acelle\Model\PaymentMethod $payment_method)
    {
        $can = $user->admin->getPermission('payment_method_create') == 'yes';

        return false;
    }

    public function update(\Acelle\Model\User $user, \Acelle\Model\PaymentMethod $payment_method)
    {
        $ability = $user->admin->getPermission('payment_method_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $payment_method->admin_id);

        return $can;
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\PaymentMethod $payment_method)
    {
        $ability = $user->admin->getPermission('payment_method_delete');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $payment_method->admin_id);

        return false;
    }

    public function disable(\Acelle\Model\User $user, \Acelle\Model\PaymentMethod $payment_method)
    {
        $ability = $user->admin->getPermission('payment_method_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $payment_method->admin_id);

        return $can && $payment_method->status != 'inactive';
    }

    public function enable(\Acelle\Model\User $user, \Acelle\Model\PaymentMethod $payment_method)
    {
        $ability = $user->admin->getPermission('payment_method_update');
        $can = $ability == 'all'
                || ($ability == 'own' && $user->admin->id == $payment_method->admin_id);

        return $can && $payment_method->status != 'active';
    }
}
