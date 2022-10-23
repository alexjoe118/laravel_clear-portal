@extends('layouts.dashboard.admin.resource-index', [
	'resource' => 'loan-request',
	'create' => false
])

@section('listing')
	@component('components.resource-listing', [
		'pagination' => $loanRequests
	])
		@foreach ($loanRequestsByUser as $user => $loanRequests)
			@include('components.page-subtitle', [
				'subtitle' => $user
			])

			@foreach ($loanRequests as $loanRequest)
				@component('components.resource-listing-item', [
					'title' => getTitle($loanRequest),
					'resource' => 'loan-request',
					'item' => $loanRequest,
					'actions' => [
						'notify',
						'edit',
						'delete' => [
							'confirmMessage' => 'The Open Approval(s) associated to this Loan Request will also be deleted.<br>The message below will be sent via email to the User.',
							'confirmFields' => [
								[
									'width' => 'full',
									'label' => 'Message',
									'type' => 'multiline',
									'input' => [
										'required' => false,
										'rows' => 2
									]
								]
							]
						]
					]
				])
					@foreach ($loanRequest->openApprovals as $openApproval)
						@include('components.resource-listing-item', [
							'resource' => 'open-approval',
							'item' => $openApproval,
							'actions' => ['notify', 'edit', 'delete']
						])
					@endforeach
				@endcomponent
			@endforeach
		@endforeach
	@endcomponent
@endsection
