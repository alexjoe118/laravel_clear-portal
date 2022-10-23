@extends('layouts.dashboard.admin.resource-index', [
	'resource' => 'document-group'
])

@section('listing')
	@component('components.resource-listing', [
		'pagination' => $documentGroups
	])
		@foreach ($documentGroups as $documentGroup)
			@include('components.resource-listing-item', [
				'resource' => 'document-group',
				'item' => $documentGroup
			])
		@endforeach
	@endcomponent
@endsection
