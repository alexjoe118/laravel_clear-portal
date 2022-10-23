@extends('layouts.auth-login', [
	'title' => 'Welcome'
])

@section('form')
	@include('components.form-fields', [
		'fields' => [
			[
				'width' => 'full',
				'type' => 'title',
				'title' => 'Thank you for your application!',
				'titleSmall' => 'For security purposes, please check your inbox to verify your email address. If you didn\'t receive our email, please request another verification link by clicking on the blue box below.'
			]
		]
	])

	<form method="POST" action="{{ route('verification.send') }}" class="send-email-verification">
		@csrf

		@include('components.button', [
			'tag' => 'button',
			'title' => 'Resend Verification Email'
		])
	</form>
@endsection
