@extends('layouts.dashboard', ['pageTitle' => 'User Account'])

@section('page')
	@include('components.form-messages')

	<form
		class="info js-form"
		method="POST"
		action="{{ route('user.update') }}"
		enctype="multipart/form-data">
		@csrf
		@method('PUT')

		<header class="general">
			@include('components.input-upload-image', [
				'defaultValue' => Auth::user()->photo,
				'style' => 'medium bordered',
				'input' => [
					'class' => 'photo',
					'name' => 'photo'
				]
			])

			<div>
				<span class="label text-extra-small">{{
					Auth::user()->isNotAdmin()
					? 'Business Info'
					: 'Personal Info'
				}}</span>

				<span class="name text-h3">{{
					Auth::user()->isNotAdmin()
						? Auth::user()->business->name
						: Auth::user()->full_name
				}}</span>
			</div>

			<span class="id text-h5">
				@if (Auth::user()->isNotAdmin())
					Account #{{ Auth::user()->customer_id }}
				@else
					{{ Str::title(Auth::user()->role) }}
				@endif
			</span>
		</header>

		<div class="inputs">
			@if (Auth::user()->isAdmin())
				@include('components.form-fields', [
					'defaults' => Auth::user(),
					'style' => 'bordered',
					'fields' => [
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
							'label' => 'Phone Number',
							'mask' => 'phone-number',
							'input' => [
								'required' => false
							]
						]
					]
				])
			@else
				@include('components.form-fields.business', [
					'title' => 'Business Information',
					'style' => 'bordered',
					'defaults' => Auth::user()
				])

				@include('components.form-fields.personal', [
					'title' => 'Owner Information',
					'titlePartners' => 'Partner Information',
					'style' => 'bordered',
					'defaults' => Auth::user()
				])
			@endif

			@include('components.button', [
				'tag' => 'button',
				'title' => 'Save Changes',
				'style' => 'primary large'
			])
		</div>
	</form>

	<div class="update-email">
		<form
			method="POST"
			action="{{ route('user.update-email', ['id' => Auth::id()]) }}">
			@csrf
			@method('PUT')

			@if (Auth::user()->new_email)
				<div class="alert">There is a request to update your email to {{ Auth::user()->new_email }}.<br>If you didn't receive our email, please request another verification link by clicking <a href="{{ route('user.notify-new-email') }}">here</a>.</div>
			@endif

			@include('components.form-fields', [
				'title' => 'Update your Email (' . Auth::user()->email . ')',
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
				'style' => 'primary large'
			])
		</form>
	</div>

	<div class="update-password">
		<form
			method="POST"
			action="{{ route('user.update-password') }}">
			@csrf
			@method('PUT')

			@include('components.form-fields', [
				'title' => 'Update your Password',
				'style' => 'bordered',
				'fields' => [
					[
						'width' => 'third',
						'label' => 'Current Password',
						'input' => [
							'type' => 'password'
						]
					],
					[
						'width' => 'third',
						'label' => 'New Password',
						'input' => [
							'type' => 'password'
						]
					],
					[
						'width' => 'third',
						'label' => 'Confirm New Password',
						'input' => [
							'type' => 'password',
							'name' => 'new_password_confirmation'
						]
					]
				]
			])

			@include('components.button', [
				'tag' => 'button',
				'title' => 'Update Password',
				'style' => 'primary large'
			])
		</form>
	</div>
@endsection
