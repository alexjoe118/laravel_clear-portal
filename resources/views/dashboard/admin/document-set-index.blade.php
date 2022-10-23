@extends('layouts.dashboard.admin.resource-index', [
	'resource' => 'document-set'
])

@section('listing')
	@component('components.resource-listing', [
		'pagination' => $documentSets
	])
		@foreach ($documentSets as $documentSet)
			@include('components.resource-listing-item', [
				'resource' => 'document-set',
				'item' => $documentSet
			])
		@endforeach
	@endcomponent
@endsection
