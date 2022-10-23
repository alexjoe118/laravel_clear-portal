@extends('dashboard.admin.resource-form', [
	'resourceType' => 'Document Group',
	'resource' => $documentGroup
])

@section('inputs')
	@include('components.form-fields', [
		'defaults' => $documentGroup,
		'fields' => [
			[
				'width' => 'full',
				'label' => 'Title'
			]
		]
	])
@endsection
