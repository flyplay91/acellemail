@extends('layouts.frontend')

@section('title', trans('messages.campaigns') . " - " . trans('messages.confirm'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/pickers/anytime.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')
			<div class="page-title">
				@include('automations._head')

				@include('automations.overview._menu', [
					'step' => 'workflow'
				])
			</div>
@endsection

@section('content')

        <div class="overview-campaign-box">
                <form action="{{ action('AutomationController@confirm', $automation->uid) }}" method="POST" class="form-validate-jqueryz">
                    {{ csrf_field() }}

                    <div class="head">
                        <h2 class="text-semibold mb-0 mt-0 text-teal-800">{{ trans('messages.workflow_overview') }}</h2>
                    </div>

                    <ul class="modern-listing automation-confirm">
                        <li>
							<table class="table">
								<tr>
									<td>
										<h4>
											{{ trans('messages.auto_event_' . $automation->getInitEvent()->event_type) }}
										</h4>
										{{ trans('messages.type') }}
									</td>
									<td>
										<h4>
											@if (is_object($automation->segment))
												{{ $automation->mailList->name }} . {{ $automation->segment->name }}: {{ $automation->readCache('SubscriberCount', 0) }} {{ strtolower(trans('messages.recipients')) }}
											@elseif (is_object($automation->mailList))
												{{ $automation->mailList->name }}: {{ $automation->readCache('SubscriberCount', 0) }} {{ strtolower(trans('messages.recipients')) }}
											@endif
										</h4>
										{{ trans('messages.recipients') }}
									</td>
									<td>
										<h4>
											{{ $automation->autoEvents()->count() }}
										</h4>
										{{ trans('messages.triggers') }}
									</td>
									<td>
										<h4>
											{{ $automation->getCampaigns()->count() }}
										</h4>
										{{ trans('messages.emails') }}
									</td>
									<td>
										<h4>
											{{ Tool::formatDateTime($automation->created_at) }}
										</h4>
										{{ trans('messages.created_at') }}
									</td>
								</tr>
							</table>
                        </li>
						<li>
                            <h4><numbericon>1</numbericon> {{ trans('messages.trigger_workflow_when_the_following_conditions_are_met') }}</h4>
							<p>
								{!! $first_event->displayMessage() !!}
							</p>
                        </li>
						@if (isset($first_event->id))
                            @foreach($first_event->originCampaigns as $key => $campaign)
                                @include('automations.overview._campaign_row', ['event' => $first_event])
                            @endforeach
						@endif

						@foreach ($automation->getFollowUpEvents(false) as $key =>  $auto_event)
							<li>
								<h4><numbericon>{{ $key+2 }}</numbericon>  {!! $auto_event->displayMessage() !!}</h4>
								<p>
									{{ trans('messages.follow_up') }}
								</p>
							</li>
							@foreach($auto_event->originCampaigns as $key => $campaign)
                                @include('automations.overview._campaign_row', ['event' => $auto_event])
                            @endforeach
						@endforeach
                    </ul>
                </form>

            </div>

			<a href="{{ action("AutomationController@index") }}" class="btn bg-grey-800">
				<i class="icon-arrow-left7"></i> {{ trans('messages.back_to_list') }}
			</a>
@endsection
