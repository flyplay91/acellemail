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

				@include('automations._steps', [
					'step' => 'confirm'
				])
			</div>
@endsection

@section('content')

        <div class="confirm-campaign-box">
                <form action="{{ action('AutomationController@confirm', $automation->uid) }}" method="POST" class="form-validate-jqueryz">
                    {{ csrf_field() }}

                    <div class="head">
                        <h2 class="text-semibold mb-5">{{ trans('messages.you_are_all_send') }}</h2>
                        <p>{{ trans('messages.review_campaign_feeback') }}</p>
                    </div>

                    <ul class="modern-listing automation-confirm">
                        <li>
                            <a href="{{ action('AutomationController@edit', $automation->uid) }}" class="btn btn-info bg-grey">{{ trans('messages.edit') }}</a>
							@if ($automation->readCache('SubscriberCount', 0))
								<i class="icon-checkmark4"></i>
							@else
								<i class="fa fa-exclamation text-warning"></i>
							@endif
                            <h4>{{ $automation->readCache('SubscriberCount', 0) }} {{ trans('messages.recipients') }}</h4>
                            <p>
								{{ $automation->displayRecipients() }}
                            </p>
                        </li>
						<li>
                            <a href="{{ action('AutomationController@trigger', $automation->uid) }}" class="btn {{ (isset($first_event->id) && $first_event->automation->readCache('SubscriberCount', 0) > 0) ? 'btn-info bg-grey' : ' btn-info bg-info-800' }}">{{ trans('messages.edit') }}</a>
							@if (isset($first_event->id) && $first_event->automation->readCache('SubscriberCount', 0) > 0)
								<i class="icon-checkmark4"></i>
							@elseif($first_event->automation->readCache('SubscriberCount', 0) <= 0)
								<i class="fa fa-exclamation text-warning"></i>
							@else
								<i class="icon-cross2 text-danger"></i>
							@endif
                            <h4><numbericon>1</numbericon> {{ trans('messages.trigger_workflow_when_the_following_conditions_are_met') }}</h4>
							<p>
								{!! $first_event->displayMessage() !!}
								@if (in_array($first_event->event_type, [Acelle\Model\AutoEvent::TYPE_CUSTOM_CRITERIA]))
									<br />
									<span class="{{ $first_event->automation->readCache('SubscriberCount', 0) <= 0 ? 'text-warning' : '' }}">
										<span class="text-bold">{{ $first_event->automation->readCache('SubscriberCount', 0) }}</span> {{ trans('messages.subscribers') }}
									</span>
								@endif
							</p>
                        </li>
						@if (isset($first_event->id))
							@forelse($first_event->campaigns as $key => $campaign)
								@include('automations._campaign_confirm_row', ['event' => $first_event])
							@empty
								@include('automations._campaign_confirm_row_empty', ['event' => $first_event])
							@endforelse
						@endif

						@foreach ($automation->getFollowUpEvents(false) as $key =>  $auto_event)
							<li>
								<a href="{{ action('AutomationController@workflow', $automation->uid) }}#event-{{ $auto_event->uid }}" class="btn btn-info bg-grey">{{ trans('messages.edit') }}</a>
								@if (isset($auto_event->id))
									<i class="icon-checkmark4"></i>
								@else
									<i class="icon-cross2 text-danger"></i>
								@endif
								<h4><numbericon>{{ $key+2 }}</numbericon>  {!! $auto_event->displayMessage() !!}</h4>
								<p>
									{{ trans('messages.follow_up') }}
								</p>
							</li>
							@forelse($auto_event->campaigns as $key => $campaign)
								@include('automations._campaign_confirm_row', ['event' => $auto_event])
							@empty
								@include('automations._campaign_confirm_row_empty', ['event' => $auto_event])
							@endforelse
						@endforeach
                    </ul>

					<div class="text-right">
						<br />
						<a href="{{ action("AutomationController@workflow", $automation->uid) }}" class="btn bg-grey-800">
							<i class="icon-arrow-left7"></i> {{ trans('messages.back_to_workflow') }}
						</a>
						@if ($automation->isValid())
							<button class="btn btn-lg bg-teal-800"><i class="icon-paperplane ml-5"></i> {{ trans('messages.send') }}</button>
						@endif
					</div>
                </form>

            </div>
@endsection
