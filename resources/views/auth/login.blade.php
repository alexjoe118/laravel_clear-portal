@extends('layouts.auth-login', [
	'title' => 'Login'
])

@section('form')
	<form method="POST" action="{{ route('login') }}">
		@csrf

		@include('components.form-fields', [
			'fields' => [
				[
					'width' => 'full',
					'input' => [
						'type' => 'email',
						'name' => 'email',
						'placeholder' => 'Email',
						'autofocus' => true
					]
				],
				[
					'width' => 'full',
					'input' => [
						'type' => 'password',
						'name' => 'password',
						'placeholder' => 'Password',
						'autocomplete' => 'current-password'
					]
				]
			]
		])

		<span class="reset-password">
			<a href="{{ route('password.request') }}">Reset Password</a>
		</span>

		@include('components.button', [
			'tag' => 'button',
			'title' => 'Login',
			'style' => 'primary large full-width'
		])

		<span class="create-account">Don’t have an account? <a href="{{ route('register') }}"><b>Create one now</b></a>.</span>
	</form>
@endsection
