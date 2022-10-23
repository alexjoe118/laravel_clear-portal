@extends('layouts.dashboard.admin.resource-index', [
	'resource' => 'notification'
])

@section('listing')
	@component('components.resource-listing', [
		'pagination' => $notifications
	])
		@foreach ($notificationsByUser as $user => $notifications)
			@include('components.page-subtitle', [
				'subtitle' => $user
			])

			@foreach ($notifications as $notification)
				@include('components.resource-listing-item', [
					'resource' => 'notification',
					'item' => $notification
				])
			@endforeach
		@endforeach
	@endcomponent
@endsection
