@extends('layouts.dashboard.admin.resource-index', [
	'resource' => 'open-approval'
])

@section('listing')
	@component('components.resource-listing', [
		'pagination' => $openApprovalsByUserAndType
	])
		@foreach ($openApprovalsByUserAndType as $user => $openApprovalsByType)
			@include('components.page-subtitle', [
				'subtitle' => $user
			])

			@foreach ($openApprovalsByType as $type => $openApprovals)
				@component('components.resource-listing-item', [
					'title' => $type,
					'resource' => 'loan-request',
					'item' => $openApprovals[0]->loanRequest,
					'actions' => []
				])
					@foreach ($openApprovals as $openApproval)
						@include('components.resource-listing-item', [
							'resource' => 'open-approval',
							'item' => $openApproval,
							'actions' => ['create', 'notify', 'edit', 'delete']
						])
					@endforeach
				@endcomponent
			@endforeach
		@endforeach
	@endcomponent
@endsection
