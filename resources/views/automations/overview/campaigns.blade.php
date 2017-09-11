@extends('layouts.frontend')

@section('title', trans('messages.campaigns') . " - " . trans('messages.confirm'))
	
@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/touch.min.js') }}"></script>
		
	<script type="text/javascript" src="{{ URL::asset('js/listing.js') }}"></script>
@endsection

@section('page_header')	
			<div class="page-title">
				<ul class="breadcrumb breadcrumb-caret position-right">
					<li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
					<li><a href="{{ action("AutomationController@index") }}">{{ trans('messages.automations') }}</a></li>
					<li>{{ trans('messages.overview') }}</li>
				</ul>
				<h1>
					<span class="text-semibold"><i class="icon-alarm-check"></i> {{ $automation->name }}</span>
				</h1>
					
				@include('automations.overview._menu', [
					'step' => 'campaigns'
				])
			</div>
@endsection

@section('content')
    
	<form class="listing-form"
		data-url="{{ action('AutomationController@overviewCampaignsList', $automation->uid) }}"
		per-page="{{ Acelle\Model\AutomatedCampaign::$itemsPerPage }}"
	>				
		<div class="row top-list-controls mt-0">
			<div class="col-md-10">
				@if ($campaigns->count() >= 0)					
					<div class="filter-box">
						<span class="text-nowrap">
							<input name="search_keyword" class="form-control search" placeholder="{{ trans('messages.type_to_search') }}" />
							<i class="icon-search4 keyword_search_button"></i>
						</span>
					</div>
				@endif
			</div>
		</div>
		
		<div class="pml-table-container">
			
			
			
		</div>
	</form>
        
@endsection
