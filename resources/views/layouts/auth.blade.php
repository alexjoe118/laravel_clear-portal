@extends('layouts.base')

@section('styles')
	<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('body')
	@include('components.header')

	<div class="auth">
		<div class="page js-page {{ $layoutName ?? '' }} {{ $viewName }}">
			@yield('page')
		</div>
	</div>

	@include('components.footer')

	<script src="{{ asset('js/auth.js') }}" defer></script>
@endsection
