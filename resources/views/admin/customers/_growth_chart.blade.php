<div class="row">
    <div class="col-md-12">
        <!-- Basic column chart -->
        <div class="panel panel-flat">
            <div class="panel-body">
                <div class="chart-container">
                    <div class="chart has-fixed-height-250" id="basic_columns" data-url="{{ action('Admin\CustomerController@growthChart') }}"></div>
                </div>
            </div>
        </div>
        <!-- /basic column chart -->
    </div>
</div>
