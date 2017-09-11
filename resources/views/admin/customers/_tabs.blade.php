<div class="tabbable">
    <ul class="nav nav-tabs nav-tabs-top">
        <li class="
        {{ request()->route()->getActionName() == 'Acelle\Http\Controllers\Admin\CustomerController@edit' ? 'active' : '' }}
        text-semibold"><a href="{{ action('Admin\CustomerController@edit', $customer->uid) }}">
            <i class="icon-user"></i> {{ trans('messages.profile') }}</a>
        </li>
        <li class="
        {{ request()->route()->getActionName() == 'Acelle\Http\Controllers\Admin\CustomerController@contact' ? 'active' : '' }}
        text-semibold"><a href="{{ action('Admin\CustomerController@contact', $customer->uid) }}">
            <i class="icon-office position-left"></i> {{ trans('messages.contact_information') }}</a>
        </li>
        <li class="
        {{ request()->route()->getActionName() == 'Acelle\Http\Controllers\Admin\CustomerController@subscriptions' ? 'active' : '' }}
        text-semibold"><a href="{{ action('Admin\CustomerController@subscriptions', $customer->uid) }}">
            <i class="icon-quill4"></i> {{ trans('messages.subscriptions') }}</a>
        </li>
        @can('viewSubAccount', $customer))
            <li class="
            {{ request()->route()->getActionName() == 'Acelle\Http\Controllers\Admin\CustomerController@subAccount' ? 'active' : '' }}
            text-semibold"><a href="{{ action('Admin\CustomerController@subAccount', $customer->uid) }}">
                <i class="icon-drive"></i> {{ trans('messages.customer.sub_account') }}</a>
            </li>
        @endcan
    </ul>
</div>
