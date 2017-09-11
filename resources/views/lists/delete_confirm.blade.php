<h6 class="mt-0 mb-0">{{ trans('messages.delete_list_confirm_warning') }}</h6>
<ul class="modern-listing mt-10">
    @foreach ($lists->get() as $list)
        <li>
            <i class="icon-cancel-circle2 text-danger"></i>
            <h4 class="text-danger">{{ $list->name }}</h4>
            <p>
                @if ($list->readCache('SubscriberCount', 0))
                    <span class="text-bold text-danger">{{ $list->readCache('SubscriberCount', 0) }}</span> {{ trans('messages.subscribers') }}<pp>,</pp>
                @endif
                @if ($list->segments()->count())
                    <span class="text-bold text-danger">{{ $list->segments()->count() }}</span> {{ trans('messages.segments') }}<pp>,</pp>
                @endif
                @if ($list->campaigns()->count())
                    <span class="text-bold text-danger">{{ $list->campaigns()->count() }}</span> {{ trans('messages.campaigns') }}<pp>,</pp>
                @endif
            </p>
        </li>
    @endforeach
</ul>
