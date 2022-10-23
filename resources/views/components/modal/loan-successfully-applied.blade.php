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
