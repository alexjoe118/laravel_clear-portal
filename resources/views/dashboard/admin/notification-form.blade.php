@extends('dashboard.admin.resource-form', [
	'resourceType' => 'Notification',
	'resource' => $notification
])

@section('inputs')
	@if ($notification->read ?? false)
		<div class="alert">This notification was marked as read already by the user. It's not a good idea to edit it now.</div>
	@endif

	@include('components.form-fields', [
		'defaults' => $notification,
		'fields' => [
			[
				'width' => 'third',
				'label' => 'User',
				'type' => 'select',
				'options' => $users,
				'input' => [
					'name' => 'user_id'
				]
			],
			[
				'width' => 'third',
				'label' => 'Title'
			],
			[
				'width' => 'third',
				'label' => 'URL',
				'input' => [
					'type' => 'text',
					'placeholder' => 'e.g. https://website.com',
					'required' => false
				]
			],
			[
				'width' => 'full',
				'label' => 'Message',
				'type' => 'multiline'
			]
		]
	])
@endsection
