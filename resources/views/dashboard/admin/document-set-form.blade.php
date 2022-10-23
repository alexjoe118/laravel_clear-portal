@extends('dashboard.admin.resource-form', [
	'resourceType' => 'Document Set',
	'resource' => $documentSet
])

@section('inputs')
	@include('components.form-fields', [
		'defaults' => $documentSet,
		'fields' => [
			[
				'width' => 'full',
				'label' => 'Title'
			]
		]
	])
@endsection
