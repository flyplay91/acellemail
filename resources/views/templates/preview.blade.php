<script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/html2canvas/html2canvas.js') }}"></script>

<script>
    function captureFullPage()
    {
        html2canvas(document.body, {
            onrendered: function(canvas)
            {
                var img = canvas.toDataURL()
                $(".saving").show();
                $.post('{{ action('TemplateController@saveImage', $template->uid) }}', {data: img, '_token': '{!! csrf_token() !!}'}, function (file) {
                    $(".saving").hide();
                    if (opener) {
                        opener.tableFilterAll();
                    }
                });
            }
        });
    }
</script>

<div class="saving" style="display:none; position: fixed;
    height: 100%;
    vertical-align: middle;
    text-align: center;
    padding: 100px 0;
    font-size: 20px;
    color: #fff;
    width: 100%;
    background: rgba(0,0,0,0.7);">{{ trans('messages.saving_screenshot') }}</div>

{!! $template->content !!}

<script>
    setTimeout('captureFullPage()', 1000);
</script>
