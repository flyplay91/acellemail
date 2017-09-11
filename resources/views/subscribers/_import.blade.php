            <hr>
			<h2 class="text-bold text-teal-800"><i class="icon-task"></i> {{ trans('messages.recent_import') }}</h2>
			<form class="listing-form"
					data-url="{{ action('SubscriberController@importList', ["list_uid" => $list->uid]) }}"
					per-page="10"					
				>				
					<div class="row top-list-controls">
						<div class="col-md-10">
							@if ($system_jobs->count() >= 0)					
								<div class="filter-box">
									<span class="filter-group">
										<span class="title text-semibold text-muted">{{ trans('messages.sort_by') }}</span>
										<select class="select" name="sort-order">
											<option value="system_jobs.created_at">{{ trans('messages.created_at') }}</option>
											<option value="system_jobs.updated_at">{{ trans('messages.updated_at') }}</option>
										</select>										
										<button class="btn btn-xs sort-direction" rel="desc" data-popup="tooltip" title="{{ trans('messages.change_sort_direction') }}" type="button" class="btn btn-xs">
											<i class="icon-sort-amount-desc"></i>
										</button>
									</span>									
								</div>
							@endif
						</div>
					</div>
					
					<div class="pml-table-container">
						
						
					</div>
				</form>
					
				<script>
					$(document).ready(function() {
						setInterval('tableRefreshAll()', 3000);
					});
				</script>