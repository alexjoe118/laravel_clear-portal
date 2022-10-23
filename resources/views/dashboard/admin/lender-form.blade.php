@extends('dashboard.admin.resource-form', [
	'resourceType' => 'Lender',
	'resource' => $lender
])

@section('inputs')
	@include('components.form-fields', [
		'defaults' => $lender,
		'fields' => [
			[
				'width' => 'third',
				'label' => 'Name'
			],
			[
				'width' => 'third',
				'label' => 'Email',
				'input' => [
					'type' => 'email'
				]
			],
			[
				'width' => 'third',
				'label' => 'Phone Number',
				'mask' => 'phone-number'
			]
		]
	])
@endsection
