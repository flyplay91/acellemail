@if ($campaigns->count() > 0)
    <table class="table table-box pml-table"
        current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
    >
        @foreach ($campaigns as $key => $campaign)
            <tr>
                <td width="1%">
                    <div class="text-nowrap">
                        <div class="checkbox inline">
                            <label>
                                <input type="checkbox" class="node styled"
                                    custom-order="{{ $campaign->custom_order }}"
                                    name="ids[]"
                                    value="{{ $campaign->uid }}"
                                />
                            </label>
                        </div>
                        @if (request()->sort_order == 'custom_order' && empty(request()->keyword))
                            <i data-action="move" class="icon icon-more2 list-drag-button"></i>
                        @endif
                    </div>
                </td>
                <td width="1%">
                    <a href="#"  onclick="popupwindow('{{ action('CampaignController@preview', $campaign->uid) }}', '{{ $campaign->name }}', 800, 800)">
                        <img class="template-thumb" width="100" height="120" src="{{ action('CampaignController@image', $campaign->uid) }}?v={{ rand(0,10) }}" />
                    </a>
                </td>
                <td>
                    <h5 class="no-margin text-bold">
                        <a class="kq_search" href="{{ action('CampaignController@show', $campaign->uid) }}">
                            {{ $campaign->name }}
                        </a>
                    </h5>
                    <span class="text-muted">{{ trans('messages.' . $campaign->type) }}</span>

                    <br />
                    @if ($campaign->status != 'new')
                        <span class="text-muted2">{{ trans('messages.run_at') }}: &nbsp;&nbsp;<i class="icon-alarm mr-0"></i> {{ isset($campaign->run_at) ? Tool::formatDateTime($campaign->run_at) : "" }}</span>
                    @else
                        <span class="text-muted2">{{ trans('messages.updated_at') }}: {{ Tool::formatDateTime($campaign->created_at) }}</span>
                    @endif
                </td>
                <td>
                    <div class="single-stat-box pull-left">
                        <span class="no-margin stat-num">{{ trans('messages.template_type_' . $campaign->template_source) }}</span>
                        <br>
                        <span class="text-muted text-nowrap">{{ trans('messages.type') }}</span>
                    </div>
                </td>
                <td class="text-right">
                    <a link-method="PATCH" href="{{ action('CampaignController@campaignTemplateChoose', ['uid' => $uid, 'from_uid' => $campaign->uid]) }}" type="button" class="btn bg-teal btn-icon">
                        <i class="icon-checkmark4"></i> {{ trans('messages.choose') }}
                    </a>
                </td>
            </tr>
        @endforeach
    </table>
    @include('elements/_per_page_select', ["items" => $campaigns])
    {{ $campaigns->links() }}
@elseif (!empty(request()->keyword))
    <div class="empty-list">
        <i class="icon-paperplane"></i>
        <span class="line-1">
            {{ trans('messages.no_search_result') }}
        </span>
    </div>
@else
    <div class="empty-list">
        <i class="icon-paperplane"></i>
        <span class="line-1">
            {{ trans('messages.campaign_empty_line_1') }}
        </span>
    </div>
@endif
