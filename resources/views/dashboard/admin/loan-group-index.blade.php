@extends('layouts.dashboard.admin.resource-index', [
	'resource' => 'loan-group'
])

@section('listing')
	@component('components.resource-listing', [
		'pagination' => $loanGroups
	])
		@foreach ($loanGroups as $loanGroup)
			@include('components.resource-listing-item', [
				'resource' => 'loan-group',
				'item' => $loanGroup
			])
		@endforeach
	@endcomponent
@endsection
