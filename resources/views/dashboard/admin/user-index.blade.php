@extends('layouts.dashboard.admin.resource-index', [
	'resource' => 'user',
	'create' => false
])

@section('listing')

	@component('components.resource-user-listing', [
		'pagination' => $users,
		'pagenum' => $countperpage,
	])

		@foreach ($users as $user)
			@include('components.resource-listing-item', [
				'resource' => 'user',
				'item' => $user,
				'actions' => [
					'edit',
					'delete' => [
						'confirmMessage' => 'All related information such as Loans, Documents, Partners etc will also be deleted.'
					]
				]
			])
		@endforeach
	@endcomponent
@endsection
