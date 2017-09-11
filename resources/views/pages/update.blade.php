@extends('layouts.frontend')

@section('title', $list->name . ": " . trans('messages.update_page', ['name' => trans('messages.' . $layout->alias)]))

@section('page_script')
	<script type="text/javascript" src="{{ URL::asset('tinymce/tinymce.min.js') }}"></script>
        
    <script type="text/javascript" src="{{ URL::asset('js/editor.js') }}"></script>
@endsection

@section('page_header')

			@include("lists._header")

@endsection

@section('content')
				
				@include("lists._menu")
				
				<h2 class="text-bold text-teal-800 mb-10"><i class="icon-certificate position-left"></i> {{ trans('messages.update_page') }}</h2>
				
                <h3>{{ trans('messages.' . $layout->alias) }}</h3>
                
                @if ($layout->alias == 'sign_up_form')
                    <p class="alert alert-info mt-20 mb-20">{{ trans('messages.sign_up_form_url') }}<br /> <a target="_blank" href="{{ action('PageController@signUpForm', ['list_uid' => $list->uid]) }}" class="text-semibold">{{ action('PageController@signUpForm', ['list_uid' => $list->uid]) }}</a></p>
                @endif
                    
                <form id="update-page" action="{{ action('PageController@update', ['list' => $list->uid, 'alias' => $layout->alias]) }}" method="POST" class="form-validate-jqueryz">
					{{ csrf_field() }}
					
					
                    
					<br />
					
					@include('helpers.form_control', [
						'type' => 'text',
						'name' => 'subject',
						'value' => $page->subject,
						'rules' => ['subject' => 'subject']])
					
                    @include('helpers.form_control', ['class' => ($layout->type == 'page' ? 'full-editor' : 'email-editor'), 'type' => 'textarea', 'name' => 'content', 'value' => $page->content, 'rules' => $list->getFieldRules()])
                    
					@if (count($layout->tags()) > 0)                                
						<div class="tags_list">                                    
							<label class="text-semibold text-teal">{{ trans('messages.required_tags') }}:</label>
							<br />
							@foreach($layout->tags() as $tag)
								@if ($tag["required"])
									<a data-popup="tooltip" title='{{ trans('messages.click_to_insert_tag') }}' href="javascript:;" class="btn btn-default text-semibold btn-xs insert_tag_button" data-tag-name="{{ $tag["name"] }}">
										{{ $tag["name"] }}
									</a>
								@endif
							@endforeach                                        
						</div>
					@endif
					
					<br />
					@if (count($layout->tags()) > 0)                                
						<div class="tags_list">                                    
							<label class="text-semibold text-teal">{{ trans('messages.available_tags') }}:</label>
							<br />
							@foreach($layout->tags() as $tag)
								@if (!$tag["required"])
									<a data-popup="tooltip" title='{{ trans('messages.click_to_insert_tag') }}' href="javascript:;" class="btn btn-default text-semibold btn-xs insert_tag_button" data-tag-name="{{ $tag["name"] }}">
										{{ $tag["name"] }}
									</a>
								@endif
							@endforeach                                        
						</div>
					@endif
					
					
					<hr />
                    <div class="">
                        <a page-url="{{ action('PageController@preview', ['list_uid' => $list->uid, 'alias' => $layout->alias]) }}" class="btn btn-info bg-grey-800 mr-10 preview-page-button" data-toggle="modal" data-target="#preview_page"><i class="icon-eye"></i> {{ trans('messages.preview') }}</a>
						<button type="submit" class="btn bg-teal mr-10"><i class="icon-check"></i> {{ trans('messages.save_change') }}</button>
                    </div>
                </form>
                
				
				<!-- Full width modal -->
				<div id="preview_page" class="modal fade">
					<div class="modal-dialog modal-full">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h5 class="modal-title"></h5>
							</div>

							<div class="modal-body">
								<iframe name="preview_page_frame" class="preview_page_frame" src="/"></iframe>
							</div>

							<div class="modal-footer">
								<button type="button" class="btn btn-info bg-grey-800" data-dismiss="modal">{{ trans('messages.close') }}</button>
							</div>
						</div>
					</div>
				</div>
				<!-- /full width modal -->
                
@endsection
