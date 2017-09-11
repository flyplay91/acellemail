@extends('layouts.frontend')

@section('title', trans('messages.campaigns') . " - " . trans('messages.setup'))
	
@section('page_script')
	<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
		
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')
	
			<div class="page-title">
				<ul class="breadcrumb breadcrumb-caret position-right">
					<li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
					<li><a href="{{ action("CampaignController@index") }}">{{ trans('messages.campaigns') }}</a></li>
				</ul>
				<h1>
					<span class="text-semibold"><i class="icon-paperplane"></i> {{ $campaign->name }}</span>
				</h1>

				@include('campaigns._steps', ['current' => 2])
			</div>

@endsection

@section('content')
                <form action="{{ action('CampaignController@setup', $campaign->uid) }}" method="POST" class="form-validate-jqueryz">
					{{ csrf_field() }}
					
					<div class="row">
						<div class="col-md-6 list_select_box" target-box="segments-select-box" segments-url="{{ action('SegmentController@selectBox') }}">
							@include('helpers.form_control', ['type' => 'text',
                                                                'name' => 'name',
                                                                'label' => trans('messages.name_your_campaign'),
                                                                'value' => $campaign->name,
                                                                'rules' => $rules,
                                                                'help_class' => 'campaign'
                                                            ])
                                                            
                            @include('helpers.form_control', ['type' => 'text',
                                                                'name' => 'subject',
                                                                'label' => trans('messages.email_subject'),
                                                                'value' => $campaign->subject,
                                                                'rules' => $rules,
                                                                'help_class' => 'campaign'
                                                            ])
                                                            
                            @include('helpers.form_control', ['type' => 'text',
                                                                'name' => 'from_name',
                                                                'label' => trans('messages.from_name'),
                                                                'value' => $campaign->from_name,
                                                                'rules' => $rules,
                                                                'help_class' => 'campaign'
                                                            ])
                            
                            @include('helpers.form_control', ['type' => 'text',
                                                                'name' => 'from_email',
                                                                'label' => trans('messages.from_email'),
                                                                'value' => $campaign->from_email,
                                                                'rules' => $rules,
                                                                'help_class' => 'campaign'
                                                            ])
                                                            
                            @include('helpers.form_control', ['type' => 'text',
                                                                'name' => 'reply_to',
                                                                'label' => trans('messages.reply_to'),
                                                                'value' => $campaign->reply_to,
                                                                'rules' => $rules,
                                                                'help_class' => 'campaign'
                                                            ])
						</div>
						<div class="col-md-6 segments-select-box">
                            <div class="form-group checkbox-right-switch">
                                @include('helpers.form_control', ['type' => 'checkbox',
                                                                'name' => 'track_open',
                                                                'label' => trans('messages.track_opens'),
                                                                'value' => $campaign->track_open,
                                                                'options' => [false,true],
                                                                'help_class' => 'campaign',
                                                                'rules' => $rules
                                                            ])
                                
                                @include('helpers.form_control', ['type' => 'checkbox',
                                                                'name' => 'track_click',
                                                                'label' => trans('messages.track_clicks'),
                                                                'value' => $campaign->track_click,
                                                                'options' => [false,true],
                                                                'help_class' => 'campaign',
                                                                'rules' => $rules
                                                            ])
                                
                                @include('helpers.form_control', ['type' => 'checkbox',
                                                                'name' => 'sign_dkim',
                                                                'label' => trans('messages.sign_dkim'),
                                                                'value' => $campaign->sign_dkim,
                                                                'options' => [false,true],
                                                                'help_class' => 'campaign',
                                                                'rules' => $rules
                                                            ])
                            </div>
						</div>
					</div>
					<hr>
					<div class="text-right">
						<button class="btn bg-teal-800">{{ trans('messages.next') }} <i class="icon-arrow-right7"></i> </button>
					</div>
					
				<form>
					
				
@endsection
