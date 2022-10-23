@extends('layouts.dashboard.admin.resource-index', [
	'resource' => 'loan-product',
	'create' => false
])

@section('listing')
	@component('components.resource-listing', [
		'pagination' => $loanProducts
	])
		@foreach ($loanProducts as $loanProduct)
			@include('components.resource-listing-item', [
				'resource' => 'loan-product',
				'item' => $loanProduct,
				'actions' => ['edit']
			])
		@endforeach
	@endcomponent
@endsection
