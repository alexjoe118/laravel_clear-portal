@extends('layouts.dashboard.admin.resource-index', [
	'resource' => 'document-type'
])

@section('listing')
	@component('components.resource-listing', [
		'pagination' => $documentTypes
	])
		@foreach ($documentTypes as $documentType)
			@include('components.resource-listing-item', [
				'resource' => 'document-type',
				'item' => $documentType
			])
		@endforeach
	@endcomponent
@endsection
