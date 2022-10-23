@extends('layouts.auth', [
	'layoutName' => 'auth-login'
])

@section('page')
	@include('components.picture', [
		'src' => globalSettings('homepage_image') ?? asset('images/auth/main-image.jpg'),
		'alt' => 'Business man walking on the streets'
	])

	<div class="form-wrapper">
		<h1 class="title text-h1">{{ $title }}</h1>

		@include('components.form-messages')

        @yield('form')

		<span class="leaf" aria-hidden="true">
			@include('svg.leaf')
		</span>
    </div>
@endsection
