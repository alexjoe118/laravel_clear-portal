@php
	$route = Route::currentRouteName();
	$method = isset($resource) ? 'PUT' : 'POST';
	$action = isset($resource)
		? route(Str::replace('edit', 'update', $route), ['id' => $resource->id])
		: route(Str::replace('create', 'store', $route));
@endphp

@extends('layouts.dashboard', [
	'pageTitle' => isset($resource) ? 'Edit - '. getTitle($resource) : 'Create ' . $resourceType
])

@section('button')
	@include('components.button', [
		'title' => 'Return to '. Str::plural($resourceType),
		'url' => route(str_replace(['edit', 'create'], 'index', $route)),
		'style' => 'secondary'
	])

	@yield('button-extra')
@endsection

@section('page')
	@if (isset($resource) && $resource->deleted_at)
		<div class="alert">This {{ $resourceType }} is currently deleted.</div>
	@endif

	<form
		method="POST"
		action="{{ $action }}"
		enctype="multipart/form-data">
		@csrf
		@method($method)

		@include('components.form-messages')

		@yield('inputs')

		@if (! isset($resource) || ! $resource->deleted_at)
			@include('components.button', [
				'tag' => 'button',
				'title' => (isset($resource) ? 'Save ' : 'Create ') . $resourceType
			])
		@endif
	</form>

	@yield('content')
@endsection
