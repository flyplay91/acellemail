                        @if (Auth::user()->admin->getPermission("setting_system_urls") == 'yes')
							<div class="tab-pane active" id="top-system_urls">
								<div class="text-left">
									<a href="{{ action("Admin\SettingController@updateUrls") }}" class="btn bg-teal">{{ trans('messages.update_urls') }}</a>
								</div>
								<br />
								<div class="">
									<ul class="modern-listing mt-0 top-border-none">
										@foreach ($settings as $name => $setting)
											@if ($setting['cat'] == 'url')
												<li>
													<i class="icon-link text-grey"></i>
													<h5 class="mt-0 mb-0 text-semibold">
														{!! str_replace("LIST_UID", "<span class='text-info-600'>LIST_UID</span>",
															str_replace("SUBSCRIBER_UID", "<span class='text-info-600'>SUBSCRIBER_UID</span>",
															str_replace("SECURE_CODE", "<span class='text-info-600'>SECURE_CODE</span>",
															str_replace("STYLE", "<span class='text-info-600'>STYLE</span>",
															str_replace("MESSAGE_ID", "<span class='text-info-600'>MESSAGE_ID</span>",
															str_replace("URL", "<span class='text-info-600'>URL</span>",
														$setting['value'])))))) !!}
													</h5>
													<p>
														{{ trans('messages.' . $name) }}
													</p>
												</li>
											@endif
										@endforeach
									</ul>
								</div>
							</div>
						@endif