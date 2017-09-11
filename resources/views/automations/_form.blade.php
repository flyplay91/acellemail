                    <div class="row">
						<div class="col-md-6">
							@include('helpers.form_control', [
								'type' => 'text',
								'name' => 'name',
                                'value' => $automation->name,
								'label' => trans('messages.Name_your_automation'),								
								'rules' => $automation->rules()["init"]
							])
						</div>
					</div>
					
					<h4 class="mb-20">
						{{ trans('messages.choose_lists_segments_for_the_campaign') }}
					</h4>
						
					<div class="addable-multiple-form">
						<div class="addable-multiple-container">
							<?php $num = 0 ?>
							@foreach ($automation->getListsSegmentsGroups() as $index =>  $lists_segment_group)
								@include('automations._list_segment_form', [
									'lists_segment_group' => $lists_segment_group,
									'index' => $num,
								])
								<?php $num++ ?>
							@endforeach
						</div>
							
						<br />
						<a
							sample-url="{{ action('AutomationController@listSegmentForm', (!$automation->uid ? '000' : $automation->uid)) }}"
							href="#add_condition" class="btn btn-info bg-info-800 add-form">
							<i class="icon-plus2"></i> {{ trans('messages.add_list_segment') }}
						</a>
					</div>