@extends('dashboard.admin.resource-form', [
	'resourceType' => 'Document',
	'resource' => $document
])

@section('inputs')
	@if ($document)
		<div class="document">
			@include('components.file', [
				'filename' => $document->filename,
				'downloadAction' => route('admin.document.download', ['id' => $document->id])
			])
		</div>

		@include('components.preview-options', [
			'options' => ['document' => 'Preview'],
			'userId' => $document->user->id
		])
	@endif

	@include('components.form-fields', [
		'defaults' => $document,
		'fields' => [
			[
				'width' => 'third',
				'label' => 'Filename'
			],
			[
				'width' => 'third',
				'label' => 'Group',
				'type' => 'select',
				'options' => $documentGroups,
				'input' => [
					'name' => 'document_group_id'
				]
			],
			[
				'width' => 'third',
				'label' => 'Document Type',
				'type' => 'select',
				'options' => $documentTypes,
				'input' => [
					'name' => 'document_type_id',
					'required' => false
				]
			]
		]
	])
@endsection
