@extends('dashboard.admin.resource-form', [
	'resourceType' => 'User',
	'resource' => $user
])

@section('inputs')
	@include('components.preview-options', [
		'label' => 'Preview from the user perspective:',
		'userId' => $user->id
	])

	@include('components.form-fields', [
		'title' => 'Advisor',
		'defaults' => $user,
		'fields' => [
			[
				'width' => 'third',
				'type' => 'select',
				'options' => $advisors,
				'input' => [
					'name' => 'advisor_id'
				]
			]
		]
	])

	@include('components.form-fields.business', [
		'title' => 'Business Information',
		'defaults' => $user
	])

	@include('components.form-fields.personal', [
		'title' => 'User Information',
		'titlePartners' => 'Partner Information',
		'defaults' => $user,
		'showPhoto' => true
	])
@endsection

@section('content')
	<div class="update-email">
		<form
			method="POST"
			action="{{ route('admin.user.update-email', ['id' => $user->id]) }}">
			@csrf
			@method('PUT')

			@if ($user->new_email)
				<div class="alert">There is a request to update the user's email to {{ $user->new_email }}.</div>
			@endif

			@include('components.form-fields', [
				'title' => 'Update user\'s Email (' . $user->email . ')',
				'style' => 'bordered',
				'fields' => [
					[
						'width' => 'half',
						'label' => 'New Email',
						'input' => [
							'type' => 'email'
						]
					],
					[
						'width' => 'half',
						'label' => 'New Email Confirmation',
						'input' => [
							'type' => 'email'
						]
					]
				]
			])

			@include('components.button', [
				'tag' => 'button',
				'title' => 'Update Email',
				'style' => 'primary'
			])
		</form>
	</div>
@endsection
