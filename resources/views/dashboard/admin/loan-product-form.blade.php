@extends('dashboard.admin.resource-form', [
	'resourceType' => 'Loan Product',
	'resource' => $loanProduct
])

@section('inputs')
	@php
		$fields = [
			[
				'width' => 'full',
				'label' => 'Loan Group',
				'type' => 'select',
				'options' => $loanGroups,
				'input' => [
					'name' => 'loan_group_id',
					'required' => false
				]
			],
			[
				'width' => 'third',
				'label' => 'Title'
			],
			[
				'width' => 'third',
				'type' => 'select',
				'label' => 'Article',
				'options' => [
					'' => '',
					'a' => 'a',
					'an' => 'an'
				]
			],
			[
				'width' => 'third',
				'label' => 'Learn More',
				'input' => [
					'type' => 'text',
					'placeholder' => 'e.g. https://website.com',
					'required' => false
				]
			],
			[
				'width' => 'full',
				'label' => 'Description',
				'type' => 'multiline',
				'input' => [
					'required' => false
				]
			],
			[
				'width' => 'full',
				'type' => 'repeater',
				'label' => 'Features',
				'buttonTitle' => 'Add Feature',
				'input' => [
					'name' => 'props',
					'placeholder' => 'Feature',
					'required' => false
				]
			],
			[
				'label' => 'Order',
				'input' => [
					'type' => 'number',
					'min' => 0,
					'required' => false
				]
			]
		];

		if ($documentTypes->isNotEmpty()) {
			$fields[] = [
				'width' => 'full',
				'type' => 'select',
				'label' => 'Required Document Types',
				'labelSmall' => 'Hold cmd/ctrl to select more than one.',
				'options' => $documentTypes,
				'input' => [
					'multiple' => true,
					'required' => false
				]
			];
		} else {
			$fields[] = [
				'width' => 'full',
				'type' => 'title',
				'label' => 'Required Document Types',
				'titleSmall' => 'There are no Document Types.
					You can create some <a href="' . route('admin.document-type.index') . '">here</a>.'
			];
		}

		if ($documentSets->isNotEmpty()) {
			$fields[] = [
				'width' => 'full',
				'type' => 'select',
				'label' => 'Required Document Sets',
				'labelSmall' => 'Hold cmd/ctrl to select more than one.',
				'options' => $documentSets,
				'input' => [
					'multiple' => true,
					'required' => false
				]
			];
		} else {
			$fields[] = [
				'width' => 'full',
				'type' => 'title',
				'label' => 'Required Document Sets',
				'titleSmall' => 'There are no Document Sets.
					You can create some <a href="' . route('admin.document-set.index') . '">here</a>.'
			];
		}
	@endphp

	@include('components.form-fields', [
		'defaults' => $loanProduct,
		'fields' => $fields
	])
@endsection
