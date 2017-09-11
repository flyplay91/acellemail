@if (is_object($plan))
    <div class="panel panel-plan box-{{ $plan->status }} box-shadow">
        <div class="panel-heading" style="background-color: {{ $plan->color }}; color: white">
            @if (!isset($readonly))
                <div class="pull-right">
                    @can('enable', $plan)
                        <a link-confirm="{{ trans('messages.enable_plans_confirm') }}" href="{{ action('Admin\PlanController@enable', ['uids' => $plan->uid]) }}" title="{{ trans('messages.enable') }}" type="button" class="btn btn-white btn-icon"><i class="icon-checkbox-unchecked2"></i></a>
                    @endcan
                    @can('disable', $plan)
                        <a link-confirm="{{ trans('messages.disable_plans_confirm') }}" href="{{ action('Admin\PlanController@disable', ['uids' => $plan->uid]) }}" title="{{ trans('messages.disable') }}" type="button" class="btn btn-white btn-icon"><i class="icon-checkbox-checked2"></i></a>
                    @endcan
                </div>
            @endif
            <h4 class="pull-left panel-title text-center">													
                {{ $plan->name }}
            </h4>
        </div>
        <div class="plan-price-box">
            @if ($plan->price == 0.0)
                <span class="price">{{ trans('messages.free') }}</span>
            @else
                <span class="price">{{ Acelle\Library\Tool::format_price($plan->price, $plan->currency->format) }}</span> /
                {{ $plan->displayFrequencyTime() }}
            @endif
                
        </div>
        <div class="panel-body pt-0">
            <ul class="mt-0 mb-0 top-border-none plans-intro">
                <li>
                    {!! trans('messages.sending_quota_intro', ["value" => $plan->displayQuota()]) !!}
                </li>
                <li>
                    {!! trans('messages.max_lists_intro', ["value" => $plan->displayMaxList()]) !!}
                </li>
                <li>
                    {!! trans('messages.max_subscribers_intro', ["value" => $plan->displayMaxSubscriber()]) !!}
                </li>
                <li>
                    {!! trans('messages.max_campaigns_intro', ["value" => $plan->displayMaxCampaign()]) !!}
                </li>
                <li>
                    {!! trans('messages.max_size_upload_total_intro', ["value" => $plan->displayMaxSizeUploadTotal()]) !!}
                </li>
                <li>
                    {!! trans('messages.max_file_size_upload_intro', ["value" => $plan->displayFileSizeUpload()]) !!}
                </li>
            </ul>
        </div>
        @if (!isset($readonly))
            <div class="panel-footer pt-10 pb-10 text-center">												
                @can('update', $plan)
                    <a href="{{ action('Admin\PlanController@edit', $plan->uid) }}" title="{{ trans('messages.edit') }}" type="button" class="btn bg-grey-800 btn-icon"><i class="icon icon-pencil pr-0 mr-0"></i></a>
                @endcan
                @can('delete', $plan)
                    <a delete-confirm="{{ trans('messages.delete_currencies_confirm') }}" href="{{ action('Admin\PlanController@delete', ['uids' => $plan->uid]) }}" title="{{ trans('messages.delete') }}" type="button" class="btn btn-danger btn-icon"><i class="icon icon-cross2"></i></a>
                @endcan												
            </div>
        @endif
    </div>
@endif