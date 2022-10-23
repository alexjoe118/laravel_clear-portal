@extends('layouts.dashboard.admin.resource-index', [
	'resource' => 'loan',
	'create' => false
])

@section('listing')
	@component('components.resource-listing', [
		'pagination' => $loans
	])
		@foreach ($loansByUser as $user => $loans)
			@include('components.page-subtitle', [
				'subtitle' => $user
			])

			@foreach ($loans as $loan)
				@include('components.resource-listing-item', [
					'resource' => 'loan',
					'item' => $loan
				])
			@endforeach
		@endforeach
	@endcomponent
@endsection
