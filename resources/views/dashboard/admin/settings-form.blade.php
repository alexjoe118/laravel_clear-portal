@extends('layouts.dashboard', [
	'pageTitle' => 'Settings'
])

@section('page')
	<form
		method="POST"
		action="{{ route('admin.settings.update') }}"
		enctype="multipart/form-data">
		@csrf
		@method('PUT')

		@include('components.form-messages')

		@include('components.form-fields', [
			'defaults' => [
				'settings' => $settings
			],
			'group' => 'settings',
			'fields' => [
				[
					'width' => 'full',
					'type' => 'title',
					'style' => ['large', 'line'],
					'title' => 'Content',
					'titleSmall' => 'Options to edit the portal\'s global visual and textual content.'
				],
				[
					'width' => 'full',
					'label' => 'Title',
					'labelSmall' => 'The page title that appears on the tab.',
					'input' => [
						'required' => false
					]
				],
				[
					'width' => 'full',
					'label' => 'Favicon',
					'type' => 'upload-image',
					'style' => 'medium squared',
					'placeholder' => 'image',
					'input' => [
						'required' => false
					]
				],
				[
					'width' => 'full',
					'label' => 'Contact Email',
					'labelSmall' => 'To be used on the header\'s links.',
					'input' => [
						'type' => 'email',
						'required' => false
					]
				],
				[
					'width' => 'full',
					'label' => 'Homepage Image',
					'labelSmall' => 'Recommended width: <code>1000px</code>.',
					'type' => 'upload-image',
					'style' => 'thumbnail squared',
					'placeholder' => 'image',
					'input' => [
						'required' => false
					]
				],
				[
					'width' => 'full',
					'label' => 'Privacy Policy',
					'input' => [
						'type' => 'text',
						'name' => 'privacy_policy_url',
						'placeholder' => 'e.g. https://website.com',
						'required' => false
					]
				],
				[
					'width' => 'full',
					'label' => 'Terms of Service',
					'input' => [
						'type' => 'text',
						'name' => 'terms_of_service_url',
						'placeholder' => 'e.g. https://website.com',
						'required' => false
					]
				],
				[
					'width' => 'full',
					'label' => 'Copyright Text',
					'input' => [
						'required' => false
					]
				],
				[
					'width' => 'full',
					'type' => 'title',
					'style' => ['large', 'line'],
					'title' => 'Scripts'
				],
				[
					'width' => 'full',
					'type' => 'multiline',
					'label' => 'Head Scripts',
					'labelSmall' => 'These will be added right before the closing <code>&lt;/head&gt;</code>.',
					'input' => [
						'required' => false
					]
				],
				[
					'width' => 'full',
					'type' => 'multiline',
					'label' => 'Body Scripts',
					'labelSmall' => 'These will be added right before the closing <code>&lt;/body&gt;</code>.',
					'input' => [
						'required' => false
					]
				],
				[
					'width' => 'full',
					'type' => 'title',
					'style' => ['large', 'line'],
					'title' => 'Users',
					'titleSmall' => 'Options related to the portal\'s users.'
				],
				[
					'width' => 'full',
					'type' => 'repeater',
					'label' => 'SSN Blacklist',
					'buttonTitle' => 'Add SSN',
					'mask' => 'ssn',
					'input' => [
						'required' => false
					]
				],
				[
					'width' => 'full',
					'type' => 'repeater',
					'label' => 'Federal Tax ID Blacklist',
					'buttonTitle' => 'Add Federal Tax ID',
					'mask' => 'federal-tax-id',
					'input' => [
						'required' => false
					]
				]
			]
		])

		@include('components.button', [
			'tag' => 'button',
			'title' => 'Save Settings'
		])
@endsection
