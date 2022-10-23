@extends('layouts.dashboard', ['pageTitle' => 'My Loans'])

@section('page')
	@include('components.form-messages')

	@if ($loans->isNotEmpty())
		@component('components.grid-boxes')
			@foreach($loans as $loan)
				@slot('box_'. $loop->iteration)
					<div class="loan-information">
						<div>
							<span class="title text-h4">{{ $loan->loanProduct->title }}</span>

							<div class="group">
								<span class="label">Funded Date:</span>
								<span class="value">{{ $loan->funded_date }}</span>

								<span class="label">
									@if ($loan->loan_amount)
										{{ $loan->loanProduct->id === 2 ? 'Funded Amount' : 'Loan Amount' }}:
									@else
										Credit Limit:
									@endif
								</span>
								<span class="value">${{ $loan->loan_amount ?? $loan->credit_limit }}</span>

								@if ($loan->term_length)
									<span class="label">Term Length:</span>
									<span class="value">{{ $loan->term_length_formatted }}</span>
								@endif

								@if ($loan->application_status)
									@if (! in_array($loan->loanProduct->id, [8, 9]))
										<span class="label">Estimated Remaining Balance:</span>
										<span class="value">${{ $loan->remaining_balance }}</span>
									@endif
								@endif

								@if ($loan->payoff_date)
									<span class="label">Payoff Date:</span>
									<span class="value">{{ $loan->payoff_date }}</span>
								@endif
							</div>

							<hr />

							@if ($loan->application_status)
								@if ($loan->estimated_renewal_date || $loan->estimated_payoff_date)
									<div class="group">
										@if ($loan->estimated_renewal_date)
											<span class="label">Estimated Renewal Date:</span>
											<span class="value">{{ $loan->estimated_renewal_date }}</span>
										@endif

										@if ($loan->estimated_payoff_date)
											<span class="label">Estimated Payoff Date:</span>
											<span class="value">{{ $loan->estimated_payoff_date }}</span>
										@endif
									</div>

									<hr />
								@endif
							@endif

							<div class="group">
								<span class="label">Lender Name:</span>
								<span class="value">{{ $loan->lender->name }}</span>

								<span class="label">Lender Phone:</span>
								<a href="tel:{{ $loan->lender->phone_number }}" class="value">{{ formatPhoneNumber($loan->lender->phone_number) }}</a>

								<span class="label">Lender E-mail:</span>
								<a href="mailto:{{ $loan->lender->email }}" class="value">{{ $loan->lender->email }}</a>

							</div>

							@if ($loan->contract_documents)
								<hr />

								<div class="group">
									<span class="label">Contract Documents:</span>

									<a
										href="{{ route('user.download-contract-documents', ['id' => $loan->id]) }}"
										rel="noopener noreferrer"
										class="value download"
										target="_blank">
										@include('svg.download')
									</a>
								</div>
							@endif
						</div>

						@if ($loan->application_status)
							<div>
								@include('components.button', [
									'tag' => 'button',
									'title' => 'Apply for ' . $loan->application_status,
									'class' => 'modal-loan-trigger',
									'attrs' => [
										'data-loan-product-id' => $loan->loanProduct->id
									]
								])
							</div>
						@endif
					</div>
				@endslot
			@endforeach
		@endcomponent

		@component ('components.modal', [
			'id' => 'modal-loan',
			'multipleTriggers' => true
		])
			@include('components.page-title', [
				'title' => "Apply for {$loan->application_status}"
			])

			<form
				method="POST"
				action="{{ route('user.loan-request.store') }}"
				enctype="multipart/form-data">
				@csrf

				@include('components.form-fields.loan-request', [
					'style' => 'bordered',
					'defaults' => collect(Auth::user())->merge([
						'loan_product_id' => $loan->loanProduct->id
					])->toArray()
				])

				@include('components.button', [
					'tag' => 'button',
					'title' => 'Submit Application',
					'style' => 'primary full-width'
				])
			</form>
		@endcomponent
	@else
		<span>You have no Loans yet.</span>
	@endif

	@if (session()->get('applied_loan'))
		@include('components.modal.loan-successfully-applied')
	@endif
@endsection
