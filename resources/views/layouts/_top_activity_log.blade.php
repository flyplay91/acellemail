                <li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<i class="icon-history"></i>
						<span class="visible-xs-inline-block position-right">{{ trans('messages.activity_log') }}</span>
					</a>
					
					<div class="dropdown-menu dropdown-content width-350">
						<div class="dropdown-content-heading">
							{{ trans('messages.activity_log') }}						
						</div>

						<ul class="media-list dropdown-content-body">
							@if (Auth::user()->customer->logs()->count() == 0)
								<li class="text-center text-muted2">
									<span href="#">
										<i class="icon-history"></i> {{ trans('messages.no_activity_logs') }}
									</span>
								</li>
							@endif
							@foreach (Auth::user()->customer->logs()->take(20)->get() as $log)
								<li class="media">
									<div class="media-left">
										<img src="{{ action('CustomerController@avatar', $log->customer->uid) }}" class="img-circle img-sm" alt="">
									</div>
	
									<div class="media-body">
										<a href="#" class="media-heading">
											<span class="text-semibold">{{ $log->customer->displayName() }}</span>
											<span class="media-annotation pull-right">{{ $log->created_at->diffForHumans() }}</span>
										</a>
	
										<span class="text-muted">{!! $log->message() !!}</span>
									</div>
								</li>
							@endforeach
							
						</ul>
						
						<div class="dropdown-content-footer">
							<a href="{{ action("AccountController@logs") }}" data-popup="tooltip" title="{{ trans('messages.all_logs') }}"><i class="icon-menu display-block"></i></a>
						</div>
					</div>
				</li>