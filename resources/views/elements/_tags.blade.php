                @if (count($tags) > 0)                                
					<div class="tags_list">                                    
						<label class="text-semibold text-teal">{{ trans('messages.required_tags') }}:</label>
						<br />
						@foreach($tags as $tag)
							@if ($tag["required"])
								<a data-popup="tooltip" title='{{ trans('messages.click_to_insert_tag') }}' href="javascript:;" class="btn btn-default text-semibold btn-xs insert_tag_button" data-tag-name="{{ "{".$tag["name"]."}" }}">
									{{ "{".$tag["name"]."}" }}
								</a>
							@endif
						@endforeach                                        
					</div>
				@endif
				
				<br />
				@if (count($tags) > 0)                                
					<div class="tags_list">                                    
						<label class="text-semibold text-teal">{{ trans('messages.available_tags') }}:</label>
						<br />
						@foreach($tags as $tag)
							@if (!$tag["required"])
								<a data-popup="tooltip" title='{{ trans('messages.click_to_insert_tag') }}' href="javascript:;" class="btn btn-default text-semibold btn-xs insert_tag_button" data-tag-name="{{ "{".$tag["name"]."}" }}">
									{{ "{".$tag["name"]."}" }}
								</a>
							@endif
						@endforeach                                        
					</div>
				@endif