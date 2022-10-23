@extends('layouts.dashboard', ['pageTitle' => 'Overview'])

@section('page')
	@if ($loanRequests->isNotEmpty())
		<div class="alert">
			<span class="message">{{ count($loanRequests) }} Loan Request(s) waiting for Open Approvals:</span>

			<div class="list">
				@foreach ($loanRequests as $loanRequest)
					<span>
						<a href="{{ route('admin.loan-request.edit', ['id' => $loanRequest->id]) }}">{{ getTitle($loanRequest) }}</a>
						requested by {{ $loanRequest->user->business->name }}
					</span>
				@endforeach
			</div>
		</div>
	@endif

	@if ($loans->isNotEmpty())
		<div class="alert">
			<span class="message">{{ count($loans) }} Loan(s) pending funding:</span>

			<div class="list">
				@foreach ($loans as $loan)
					<span>
						<a href="{{ route('admin.loan.edit', ['id' => $loan->id]) }}">{{ getTitle($loan) }}</a>
						by {{ $loan->user->business->name }}
					</span>
				@endforeach
			</div>
		</div>
	@endif

	@if ($loanRequests->isEmpty() && $loans->isEmpty())
		<div class="alert success">
			<span>There are no pendencies.</span>
		</div>
	@endif
@endsection
