<div class="row">
    <div class="col-md-12">
        @if (\Auth::user()->admin->getAllSubscriptions()->count())
            <!-- Basic column chart -->
            <div class="panel panel-flat">
                <div class="panel-body">
                    <div class="chart-container">
                        <div class="chart has-fixed-height-250"
                            id="basic_columns_pie"
                            data-url="{{ action('Admin\PlanController@pieChart') }}"
                        ></div>
                    </div>
                </div>
            </div>
            <!-- /basic column chart -->
        @else
            <div class="empty-chart-pie">
                <div class="empty-list has-fixed-height-300">
                    <i class="icon-file-text2"></i>
                    <span class="line-1">
                        {{ trans('messages.log_empty_line_1') }}
                    </span>
                </div>
            </div>
        @endif
    </div>
</div>
