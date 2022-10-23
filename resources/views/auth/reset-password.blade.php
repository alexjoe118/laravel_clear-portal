@extends('layouts.auth-login', [
	'title' => 'Reset Password'
])

@section('form')
	<form method="POST" action="{{ route('password.update') }}">
		@csrf

		<input type="hidden" name="token" value="{{ $request->route('token') }}">

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
						'placeholder' => 'Password'
					]
				],
				[
					'width' => 'full',
					'input' => [
						'type' => 'password',
						'name' => 'password_confirmation',
						'placeholder' => 'Confirm Password'
					]
				]
			]
		])

		@include('components.button', [
			'tag' => 'button',
			'title' => 'Reset Password',
			'style' => 'primary large full-width'
		])
	</form>
@endsection
