@extends('layouts.dashboard.admin.resource-index', [
	'resource' => 'manager'
])

@section('listing')
	@component('components.resource-listing', [
		'pagination' => $managers
	])
		@foreach ($managers as $manager)
			@include('components.resource-listing-item', [
				'resource' => 'manager',
				'item' => $manager
			])
		@endforeach
	@endcomponent
@endsection
