@extends('layouts.frontend')

@section('title', trans('messages.campaigns') . " - " . trans('messages.template'))
	
@section('page_script')
	<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>        
    <script type="text/javascript" src="{{ URL::asset('tinymce/tinymce.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/touch.min.js') }}"></script>
        
    <script type="text/javascript" src="{{ URL::asset('js/editor.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/listing.js') }}"></script>	
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

				@include('campaigns._steps', ['current' => 3])
			</div>

@endsection

@section('content')
            
            <div class="pull-right">
				@if ($campaign->template_source == 'builder')
					<a href="{{ action('CampaignController@templateRebuild', $campaign->uid) }}" type="button" class="btn bg-info-800 mr-10">
						<i class="icon icon-pencil"></i> {{ trans('messages.edit') }}
					</a>
				@endif
				@if ($campaign->type != 'plain-text')
					<a href="{{ action('CampaignController@templateSelect', $campaign->uid) }}" class="btn btn-info bg-teal-800">
						<i class="icon-loop"></i> {{ trans('messages.change_template') }}
					</a>
				@endif
            </div>
    
			<form action="{{ action('CampaignController@template', $campaign->uid) }}" method="POST" class="form-validate-jqueryz">
				{{ csrf_field() }}

				<h2 class="mt-0">{{ trans('messages.email_content') }}</h2>
				
				<ul class="nav nav-tabs nav-tabs-top top-divided text-semibold">
					@if ($campaign->type != 'plain-text')
						<li class="active">
							<a href="#top-justified-divided-tab1" data-toggle="tab">
								<i class="icon-circle-code"></i> {{ trans('messages.html_version') }}
							</a>
						</li>
					@endif
					<li class="plain_text_li {{ ($campaign->type == 'plain-text') ? " active" : "" }}">
						<a href="#top-justified-divided-tab2" data-toggle="tab">
							<i class="icon-file-text2"></i> {{ trans('messages.plain_text_version') }}
						</a>
					</li>                       
				</ul>
				
				<div class="tab-content">
					
					@if ($campaign->type != 'plain-text')
						<div class="tab-pane active" id="top-justified-divided-tab1">
                        
                            @if(!$campaign->unsubscribe_url_valid())
                                <span class="text-semibold text-danger">
                                    {{ trans('messages.unsubscribe_url_required') }}
                                </span>
                            @endif
                        
                            @if ($campaign->template_source == 'builder')
                                <textarea class="hide" name="html">{{ $campaign->html }}</textarea>
                                <iframe class="template_preview" src="{{ action('CampaignController@templateIframe', $campaign->uid) }}"></iframe>
                            @else
                                @include('helpers.form_control', ['type' => 'textarea',
                                    'class' => 'clean-editor',
                                    'name' => 'html',
                                    'label' => '',
                                    'value' => $campaign->html,
                                    'rules' => $rules,
                                    'help_class' => 'campaign'
                                ])
                            @endif
						</div>
					@endif

					<div class="tab-pane{{ ($campaign->type == 'plain-text') ? " active" : "" }}" id="top-justified-divided-tab2">
						@include('helpers.form_control', ['type' => 'textarea',
                            'class' => 'form-control plain_text_content',
                            'name' => 'plain',
                            'label' => '',
                            'value' => $campaign->plain,
                            'rules' => $rules,
                            'help_class' => 'campaign'
                        ])
					</div>
				</div>

                @if ($campaign->template_source != 'builder')
                    @include('elements._tags', ['tags' => Acelle\Model\Template::tags($campaign->defaultMailList)])
                @endif
                    
				<hr>
				<div class="text-right">
					<button class="btn bg-teal-800">{{ trans('messages.next') }} <i class="icon-arrow-right7"></i> </button>
				</div>
				
			<form>
			
			
			
@endsection
