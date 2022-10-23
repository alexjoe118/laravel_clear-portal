@extends('dashboard.admin.resource-form', [
	'resourceType' => Str::title($adminType),
	'resource' => $admin
])

@section('inputs')
	@include('components.form-fields', [
		'defaults' => $admin,
		'fields' => [
			[
				'width' => 'full',
				'type' => 'upload-image',
				'style' => 'large',
				'input' => [
					'name' => 'photo',
					'required' => false
				]
			],
			[
				'width' => 'third',
				'label' => 'First Name'
			],
			[
				'width' => 'third',
				'label' => 'Last Name'
			],
			[
				'width' => 'third',
				'label' => 'Email',
				'input' => [
					'type' => 'email'
				]
			],
			[
				'width' => 'half',
				'label' => 'Phone Number',
				'mask' => 'phone-number',
				'input' => [
					'required' => false
				]
			],
			[
				'width' => 'half',
				'label' => 'Cell Phone Number',
				'mask' => 'phone-number',
				'input' => [
					'required' => false
				]
			]
		]
	])
@endsection
