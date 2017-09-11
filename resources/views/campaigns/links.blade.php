@extends('layouts.frontend')

@section('title', $campaign->name)
	
@section('page_script')    

@endsection

@section('page_header')
	
			@include("campaigns._header")

@endsection

@section('content')
                
            @include("campaigns._menu")

            <h3 class="mt-10"><span class="text-teal text-semibold">{{ count($campaign->getLinks()) }}</span> {{ trans('messages.links') }}</h3>
            
            <table class="table table-box pml-table table-head">
                <tr>
                    <th>{{ trans('messages.url') }}</th>
                    <th class="text-right">{{ trans('messages.total_clicks') }}</th>
                    <th class="text-right">{{ trans('messages.last_clicked') }}</th>
                </tr>
				@foreach ($campaign->getLinks() as $link)
					<tr>
						<td>
							<a class="url-truncate" title="{{ $link->url }}" href="{{ $link->url }}" target="_blank">
								{{ $link->url }}
							</a>
						</td>
						<td class="text-right">
							{{ $link->clicks(null, $campaign)->count() }}
						</td>
						<td class="text-right">
							{{ isset($link->lastClick(null, $campaign)->created_at) ? Acelle\Library\Tool::formatDateTime($link->lastClick(null, $campaign)->created_at) : "" }}
						</td>
					</tr>
				@endforeach
            </table>
			<br />
			<div class="text-right">
				<a href="{{ action('CampaignController@clickLog', $campaign->uid) }}" class="btn btn-info bg-teal-600">{{ trans('messages.click_log') }} <i class="icon-arrow-right8"></i></a>
			</div>
@endsection
