@extends('layouts.base')

@section('styles')
	<link rel="stylesheet" href="{{ asset('css/landing.css') }}">
@endsection

@section('body')
	<div class="landing">
		@include('components.header')

		<div class="page js-page {{ $viewName }}">
			@yield('page')
		</div>

		@include('components.footer')
	</div>

	<script src="{{ asset('js/landing.js') }}" defer></script>
@endsection
