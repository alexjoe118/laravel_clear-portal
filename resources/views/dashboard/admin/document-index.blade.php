@extends('layouts.dashboard.admin.resource-index', [
	'resource' => 'document',
	'create' => false
])

@section('listing')
	@component('components.resource-listing', [
		'pagination' => $documents
	])
		@foreach ($documentsByUser as $user => $documents)
			@include('components.page-subtitle', [
				'subtitle' => $user
			])

			@foreach ($documents as $document)
				@include('components.resource-listing-item', [
					'resource' => 'document',
					'item' => $document
				])
			@endforeach
		@endforeach
	@endcomponent
@endsection
