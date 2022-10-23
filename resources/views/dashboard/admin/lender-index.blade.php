@extends('layouts.dashboard.admin.resource-index', [
	'resource' => 'lender'
])

@section('listing')
	@component('components.resource-listing', [
		'pagination' => $lenders
	])
		@foreach ($lenders as $lender)
			@include('components.resource-listing-item', [
				'resource' => 'lender',
				'item' => $lender
			])
		@endforeach
	@endcomponent
@endsection
