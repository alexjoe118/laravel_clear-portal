@extends('layouts.dashboard', ['pageTitle' => 'Apply for a Loan'])

@if (Auth::user()->openApprovals()->exists())
	@section('button')
		<div class="button-background">
			@include('svg.leaf')

			<span class="text-h3">Open Approvals</span>

			@include('components.button', [
				'title' => 'See All',
				'url' => route('user.open-approval.index')
			])
		</div>
	@endsection
@endif

@section('page')
	@include('components.form-messages')

	@foreach ($loanGroups as $loanGroup => $loanProducts)
		@component('components.grid-boxes', ['title' => $loanGroup])
			@foreach ($loanProducts as $loanProduct)
				@slot('box_'. $loop->iteration)
					<div class="loan-information">
						<span class="title text-h4">{{ $loanProduct->title }}</span>

						@if ($loanProduct->description)
							<p class="description text-extra-small">
								{{ $loanProduct->description }}
							</p>
						@endif

						@if ($loanProduct->props && count($loanProduct->props) > 0)
							<ul class="props text-extra-small">
								@foreach($loanProduct->props as $prop)
									<li>{{ $prop }}</li>
								@endforeach
							</ul>
						@endif

						<div class="actions">
							@if ($loanProduct->learn_more)
								<a
									href="{{ $loanProduct->learn_more }}"
									target="_blank"
									class="learn-more text-small">
									Learn More
								</a>
							@endif

							@if ($loanProduct->in_progress)
								@if ($loanProduct->is_closing)
									<div class="closing-in-progress text-small">
										@include('svg.circle-dash-check')
										<span>Closing in Progress</span>
									</div>
								@elseif ($loanProduct->has_approvals)
									@include('components.button', [
										'title' => 'Your Approvals',
										'url' => route('user.open-approval.index')
									])
								@else
									<div class="in-progress text-small">
										@include('svg.circle-dash-gear')
										<span>Application in Progress</span>
									</div>
								@endif
							@else
								@include('components.button', [
									'tag' => 'button',
									'title' => 'Apply',
									'class' => 'modal-loan-product-trigger',
									'attrs' => [
										'data-loan-product-id' => $loanProduct->id
									]
								])
							@endif
						</div>
					</div>
				@endslot
			@endforeach
		@endcomponent
	@endforeach

	@component ('components.modal', [
		'id' => 'modal-loan-product',
		'multipleTriggers' => true
	])
		@include('components.page-title', ['title' => 'Apply for a Loan'])

		<form
			method="POST"
			action="{{ route('user.loan-request.store') }}"
			enctype="multipart/form-data">
			@csrf

			@include('components.form-fields.loan-request', [
				'style' => 'bordered',
				'defaults' => collect(Auth::user())->merge([
					'loan_product_id' => $loanProduct->id
				])->toArray()
			])

			@include('components.button', [
				'tag' => 'button',
				'title' => 'Submit Application',
				'style' => 'primary full-width'
			])
		</form>
	@endcomponent

	@if (session()->get('applied_loan'))
		@component('components.modal', [
			'class' => 'active successfully-applied',
			'close' => false
		])
			<div class="title">
				@include('svg.leaf')
				<h2 class="text-h2">Congratulations!</h2>
			</div>

			<div class="success-message">
				<span class="text-h4">You have successfully applied for {{ session()->get('applied_loan') }}</span>
				<p>Please check your portal and/or inbox for approval notifications or reach out to your loan advisor for updates.</p>
			</div>

			@if ($userAdvisor)
				<hr />

				<div class="advisor">
					<span>Loan Advisor: {{ $userAdvisor->full_name }}</span>
					<span>Email: <a href="mailto:{{ $userAdvisor->email }}">{{ $userAdvisor->email }}</a></span>

					@if ($userAdvisor->phone_number)
						<span>
							Phone: <a href="tel:{{ $userAdvisor->phone_number }}">{{ formatPhoneNumber($userAdvisor->phone_number) }}</a>
						</span>
					@endif
				</div>
			@endif

			@include('components.button', [
				'tag' => 'button',
				'style' => 'large primary full-width',
				'title' => 'Back to Account',
				'class' => 'js-modal-close'
			])
		@endcomponent
	@endif
@endsection
