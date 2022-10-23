@extends('layouts.base')

@section('styles')
	<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('body')
	<div class="dashboard js-dashboard">
		@include('layouts.dashboard.sidebar')

		<main class="wrapper">
			@include('components.header', [
				'structure' => 'dashboard'
			])

			<div id="page" class="page js-page {{ $viewName }}">
				@isset($pageTitle)
					@component('components.page-title', ['title' => $pageTitle])
						@yield('button')
					@endcomponent
				@endisset

				@yield('page')
			</div>

			@include('components.footer')
		</main>
	</div>

	<script src="{{ asset('js/dashboard.js') }}" defer></script>
@endsection
