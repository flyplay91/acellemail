<h4>{{ trans('messages.delete_plan_confirm_warning') }}</h4>
<ul class="modern-listing">
    @foreach ($plans->get() as $plan)
        <li>
            <i class="icon-cancel-circle2 text-danger"></i>
            <h4 class="text-danger">{{ $plan->name }}</h4>
            <p>
                @if ($plan->subscriptionsCount())
                    <span class="text-bold text-danger">{{ $plan->subscriptionsCount() }}</span> {{ trans('messages.subscription') }}<pp>,</pp>
                @endif
            </p>                        
        </li>
    @endforeach
</ul>