@extends('layouts.dashboard.admin.resource-index', [
	'resource' => 'advisor'
])

@section('listing')
	@component('components.resource-listing', [
		'pagination' => $advisors
	])
		@foreach ($advisors as $advisor)
			@include('components.resource-listing-item', [
				'resource' => 'advisor',
				'item' => $advisor
			])
		@endforeach
	@endcomponent
@endsection
