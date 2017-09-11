<div class="row">
    <div class="col-md-6">
        <!-- Basic column chart -->
        <div class="panel panel-flat">
            <div class="panel-body">
                <div class="chart-container">
                    <div class="chart has-fixed-height" id="basic_columns" data-url="{{ action('MailListController@listGrowthChart', $list->uid) }}"></div>
                </div>
            </div>
        </div>
        <!-- /basic column chart -->
    </div>
    <div class="col-md-6">
        @if ($list->readCache('SubscriberCount') || (!isset($list->id) && Auth::user()->customer->readCache('SubscriberCount')))
            <!-- Basic column chart -->
            <div class="panel panel-flat">
                <div class="panel-body">
                    <div class="chart-container">
                        <div class="chart has-fixed-height" id="basic_columns_pie" data-url="{{ action('MailListController@statisticsChart', $list->uid) }}"></div>
                    </div>
                </div>
            </div>
            <!-- /basic column chart -->
        @else
            <div class="empty-chart-pie">
                <div class="empty-list">
                    <i class="icon-file-text2"></i>
                    <span class="line-1">
                        {{ trans('messages.log_empty_line_1') }}
                    </span>
                </div>
            </div>
        @endif

    </div>
</div>
