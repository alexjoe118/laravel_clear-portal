@extends('layouts.auth-login', [
	'title' => 'Reset Password'
])

@section('form')
	<form method="POST" action="{{ route('password.email') }}">
		@csrf

		@include('components.form-fields', [
			'fields' => [
				[
					'width' => 'full',
					'type' => 'title',
					'title' => 'Forgot your password?',
					'titleSmall' => 'Just fill the field below with your email and we will send you a password reset link that will allow you to choose a new one.'
				],
				[
					'width' => 'full',
					'input' => [
						'type' => 'email',
						'name' => 'email',
						'placeholder' => 'Email',
						'autofocus' => true
					]
				]
			]
		])

		@include('components.button', [
			'tag' => 'button',
			'title' => 'Send Reset Link',
			'style' => 'primary large full-width'
		])
	</form>
@endsection
