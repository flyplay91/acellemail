@extends('layouts.' . $view)

@section('title', trans('messages.API_Documentation'))

@section('content')

    <div class="api-page">
        <h1>{{ trans('messages.API_Documentation') }}</h1>
        <p class="alert alert-info">{!! trans('messages.api_token_guide', ["link" => action("Api\MailListController@index", ["api_token" => "YOUR_API_TOKEN"])]) !!}</p>

        @foreach (\Acelle\Library\ApiHelper::docs() as $box)
            @if ($box['view'] == $view)
                <h2 class="mt-40 mb-20" style='text-transform: uppercase;'>{{ $box['title'] }}</h2>
                <table class="table table-box pml-table table-log">
                    <tr>
                        <th width="1%" class="text-nowrap">{{ trans('messages.HTTP_method') }}</th>
                        <th width="40%">{{ trans('messages.Endpoint') }}</th>
                        <th>{{ trans('messages.Function') }}</th>
                    </tr>
                    @foreach ($box['functions'] as $function)
                        <tr>
                            <td>
                                <span class="label label-flat {{
                                    $function['method'] == 'POST' ?
                                    'bg-primary' :
                                    ($function['method'] == 'GET' ?
                                    'bg-info' :
                                    ($function['method'] == 'PATCH' ?
                                    'bg-success' : 'bg-danger'))
                                }}">{{ trans('messages.' . $function['method']) }}</span>
                            </td>
                            <td>
                              <a href="#more" class="toogle-api">{{ $function['uri'] }}</a>
                            </td>
                            <td>
                                {{ $function['description'] }}
                            </td>
                        </tr>
                        <tr style="display:none;background: #f6f6f6">
                            <td></td>
                            <td>
                                <div>
                                    <div class="description detailed">
                                        @if ($function['parameters'])
                                            <h4>{{ trans('messages.parameters') }}</h4>
                                                <div class="list"><dl>
                                                    @foreach ($function['parameters'] as $parameter)
                                                        <dt><var>{{ $parameter['name'] }}
                                                            @if (isset($parameter['optional']))
                                                                 &nbsp;&nbsp;<span class="text-muted2 text-normal">{{ trans('messages.optional') }}

                                                                @if (isset($parameter['default']))
                                                                     - default: {{ $parameter['default'] }}
                                                                @endif
                                                                </span>
                                                            @endif
                                                        </var></dt></dt>
                                                        <dd>{!! $parameter['description'] !!}</dd>
                                                    @endforeach
                                                </dl></div>
                                        @endif
                                        <h4>{{ trans('messages.returns') }}</h4>
                                        <div class="list">
                                            {{ $function['returns'] }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                              <td>
                                  <h4>Example:</h4>
                                  <pre class=""><code>{!! $function['example'] !!}</code></pre>
                              </td>
                        </tr>
                    @endforeach
                </table>
                <hr>
            @endif
        @endforeach

    </div>

    <script>
      $(document).ready(function() {
        $(".toogle-api").click(function() {
          $(this).parents("tr").next().toggle();
        });
      });
    </script>

@endsection
