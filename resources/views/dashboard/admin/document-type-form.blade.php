@extends('dashboard.admin.resource-form', [
	'resourceType' => 'Document Type',
	'resource' => $documentType
])

@section('inputs')
	@include('components.form-fields', [
		'defaults' => $documentType,
		'fields' => [
			[
				'width' => 'half',
				'label' => 'Title'
			],
			[
				'width' => 'half',
				'type' => 'select',
				'label' => 'Document Set',
				'options' => $documentSets,
				'input' => [
					'name' => 'document_set_id',
					'multiple' => false,
					'required' => false
				]
			]
		]
	])
@endsection
