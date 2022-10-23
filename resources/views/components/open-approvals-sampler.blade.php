@php
	$apply = $apply ?? true;
@endphp

<div class="open-approvals-sampler js-open-approvals-sampler">
	@if ($loanProducts->isNotEmpty())
		<div class="loan-product-groups">
			@foreach ($loanProducts as $loanProduct)
				<div class="loan-product-group js-loan-product-group" data-loan-product-id="{{ $loanProduct->id }}">
					@component('components.grid-boxes', [
						'title' => $loanProduct->title,
						'slider' => true
					])
						@foreach ($loanProduct->openApprovals as $openApproval)
							@slot('box_'. $loop->iteration)
								@if ($apply)
									<form
										class="open-approval js-open-approval"
										data-open-approval-id="{{ $openApproval->id }}"
										data-loan-product-id="{{ $openApproval->loan_product_id }}"
										data-action="{{ route('user.loan.store') }}"
										method="POST">
										@csrf
								@else
									<div
										class="open-approval js-open-approval"
										data-open-approval-id="{{ $openApproval->id }}"
										data-loan-product-id="{{ $openApproval->loan_product_id }}">
								@endif

									<input type="hidden" class="js-term-length" name="term_length" value="{{ $openApproval->term_length }}" />
									<input type="hidden" name="term_length_display" value="{{ $openApproval->term_length_display }}" />
									<input type="hidden" name="loan_request_id" value="{{ $openApproval->loan_request_id }}" />
									<input type="hidden" name="open_approval_id" value="{{ $openApproval->id }}" />
									<input type="hidden" name="loan_product_id" value="{{ $openApproval->loan_product_id }}" />

									@if ($openApproval->maximum_amount)
										<input type="hidden" class="js-maximum-amount" value="{{ $openApproval->maximum_amount }}" />
									@endif

									{{--
										1: Short Term Loan
										2: Revenue Advance
									--}}
									@if (in_array($openApproval->loan_product_id, [1, 2]))
										<span class="term-length text-h4">{{ $openApproval->term_length_formatted }}</span>

										<span class="amount text-h3 js-main-total-funding">$00,00</span>

										<ul class="informations text-extra-small">
											@if ($openApproval->cost_of_capital_display)
												<li>Cost of Capital: {{ $openApproval->cost_of_capital }}%/month</li>
											@endif
											<li>Closing Costs: {{ $openApproval->closing_costs_formatted }}</li>
											@if ($openApproval->prepayment_discount)
												<li>Prepayment Discount: Yes</li>
											@endif
											<li>Approval Expires: {{ $openApproval->approval_expires }}</li>
										</ul>

										<div class="detailed-amount">
											<div class="card-small-wrapper">
												<div>
													<span class="label text-caption">Total Funding Amount</span>
													<span class="total-funding js-total-funding">$00,00</span>
												</div>

												<div>
													<span class="label text-caption">Total Payback Amount</span>
													<span class="total-payback js-total-payback">$00,00</span>
												</div>
											</div>
										</div>

										<div class="payment">
											<div class="card-small-wrapper">
												<span class="label text-small">{{ Str::title($openApproval->payment_frequency) }} Payment</span>
												<b class="text-normal payment-portion js-payment-portion">$00.00</b>
											</div>
										</div>

										<input type="hidden" class="js-rate" value="{{ $openApproval->rate }}" />
										<input type="hidden" class="js-number-of-payments" value="{{ $openApproval->number_of_payments }}" />
										<input type="hidden" class="js-loan-amount" name="loan_amount" value="0" />

									{{--
										3: SBA Loan
										4: ABL Loan
										5: Term Loan
										6: CRE Loan
										7: Equipment Financing
									--}}
									@elseif (in_array($openApproval->loan_product_id, [3, 4, 5, 6, 7]))
										<span class="term-length text-h4">{{ $openApproval->term_length_formatted }}</span>

										<span class="amount text-h3 js-main-total-funding">$00,00</span>

										<ul class="informations text-extra-small">
											<li><b>Interest Rate: {{ $openApproval->interest_rate }}%</b></li>
											<li>Closing Costs: {{ $openApproval->closing_costs_formatted }}</li>
											@if ($openApproval->prepayment_discount)
												<li>Prepayment Discount: Yes</li>
											@endif
											<li>Approval Expires: {{ $openApproval->approval_expires }}</li>
										</ul>

										<div class="payment">
											<div class="card-small-wrapper">
												<span class="label text-small">Estimated {{ Str::title($openApproval->payment_frequency) }} Payment</span>
												<b class="text-normal payment-portion js-payment-portion">$00.00</b>
											</div>
										</div>

										<input type="hidden" class="js-term-length" value="{{ $openApproval->term_length }}" />
										<input type="hidden" class="js-interest-rate" value="{{ $openApproval->interest_rate }}" />
										<input type="hidden" class="js-loan-amount" name="loan_amount" value="0" />

									{{--
										8: Line of Credit
									--}}
									@elseif ($openApproval->loan_product_id === 8)
										<span class="term-length text-h4">{{ $openApproval->term_length_formatted }}</span>

										<span class="amount text-h3">${{ number_format($openApproval->maximum_amount) }}</span>

										<ul class="informations text-extra-small">
											<li><b>Interest Rate: {{ $openApproval->interest_rate }}%</b></li>
											<li>Closing Costs: {{ $openApproval->closing_costs_formatted }}</li>
											<li>Draw Fee: {{ $openApproval->draw_fee }}%</li>
											@if ($openApproval->prepayment_discount)
												<li>Prepayment Discount: Yes</li>
											@endif
											<li>Approval Expires: {{ $openApproval->approval_expires }}</li>
										</ul>

										<div class="detailed-amount">
											<div class="card-small-wrapper">
												<div>
													<span class="label text-caption">Sample Draw Amount</span>
													<span class="total-funding js-sample-draw">$00,00</span>
												</div>
											</div>
										</div>

										<div class="payment">
											<div class="card-small-wrapper">
												<span class="label text-small">{{ Str::title($openApproval->payment_frequency) }} Payment</span>
												<b class="text-normal payment-portion js-payment-portion">$00.00</b>
											</div>
										</div>

										<input type="hidden" class="js-multiplier" value="{{ $openApproval->multiplier }}" />
										<input type="hidden" class="js-number-of-payments" value="{{ $openApproval->number_of_payments }}" />
										<input type="hidden" name="credit_limit" value="{{ $openApproval->maximum_amount }}" />

									{{--
										9: Invoice Factoring
									--}}
									@elseif ($openApproval->loan_product_id === 9)
										<span class="amount text-h3">${{ number_format($openApproval->total_credit_limit) }}</span>

										<ul class="informations text-extra-small">
											<li>Total Credit Limit: ${{ number_format($openApproval->total_credit_limit) }}</li>
											<li>Weekly Rate: {{ $openApproval->factor_rate }}%</li>
											<li>Maximum Advance: ${{ number_format($openApproval->maximum_advance) }}</li>
											<li>Misc Fees: ${{ number_format($openApproval->misc_fees) }}</li>
											<li>Approval Expires: {{ $openApproval->approval_expires }}</li>
										</ul>

										<input type="hidden" name="credit_limit" value="{{ $openApproval->total_credit_limit }}" />
									@endif

									@if ($openApproval->notes())
										<div class="notes">
											<span class="label text-caption">Additional Notes</span>

											<p class="text-extra-small">{{ $openApproval->notes() }}</p>
										</div>
									@endif

								@if ($apply)
									@include('components.button', [
										'tag' => 'button',
										'confirm' => true,
										'title' => 'Select',
										'style' => 'primary full-width'
									])

									</form>
								@else
									</div>
								@endif
							@endslot
						@endforeach
					@endcomponent

					@if ($loanProduct->id !== 9)
						@php
							$maximumAmount = $loanProduct->openApprovals
								->pluck('maximum_amount')
								->reduce(function($prevMaximumAmount, $maximumAmount) {
									if (! $prevMaximumAmount) return $maximumAmount;

									return $maximumAmount > $prevMaximumAmount
										? $maximumAmount
										: $prevMaximumAmount;
								});
						@endphp

						<div class="toggle-bar js-toggle-bar">
							<span class="label">10k</span>

							<div class="bar">
								<div class="track js-track"></div>
								<div class="thumb js-thumb">
									<div class="status text-small js-status">{{ number_format(0) }}</div>
									@include('svg.bars')
								</div>

								<input
									type="range"
									name="range"
									min="10000"
									max="{{ $maximumAmount }}"
									value="{{ $maximumAmount }}"
									step="1000">
							</div>

							<span class="label">{{
								($maximumAmount / 1000) < 1000
								? $maximumAmount / 1000 . 'K'
								: $maximumAmount / 1000000 . 'M'
							}}</span>
						</div>
					@endif
				</div>
			@endforeach
		</div>

		@if (session()->get('applied_open_approval'))
			@component('components.modal', [
				'class' => 'active successfully-applied',
				'close' => false
			])
				<div class="title">
					@include('svg.leaf')
					<h2 class="text-h2">Congratulations!</h2>
				</div>

				<div class="success-message">
					<span class="text-h4">We'll now work to expedite funding. Please check your portal and/or inbox for next steps or contact your loan advisor for an update.</span>
				</div>

				@include('components.button', [
					'tag' => 'button',
					'style' => 'large primary full-width',
					'title' => 'Back to Account',
					'class' => 'js-modal-close'
				])
			@endcomponent
		@endif

		@if (count($loanProducts) > 1)
			<div class="scroll-indicator js-scroll-indicator active">
				@include('svg.arrow-right')
			</div>
		@endif
	@else
		<span class="empty-message">No Approvals available yet.</span>
	@endif
</div>
