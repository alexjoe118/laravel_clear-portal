@extends('layouts.auth')

@section('page')
	<div class="wrapper">
		@component('components.page-title', ['title' => 'Loan Request'])
			<div class="steps">
				@foreach(range(1, 3) as $step)
					<div
						class="step js-step {{ $loop->first ? 'active' : '' }}"
						data-index="{{ $loop->index }}">

						<div class="step-circle">
							<span>0{{ $step }}</span>
							@include('svg.check')
						</div>
					</div>
				@endforeach
			</div>
		@endcomponent

		@include('components.form-messages')

		<form
			method="POST"
			data-step-action="{{ route('register.step') }}"
			action="{{ route('register') }}"
			class="register js-register"
			enctype="multipart/form-data">
			@csrf

			<div class="swiper js-swiper">
				<div class="swiper-wrapper">
					<fieldset class="swiper-slide js-swiper-slide" data-title="Loan Request ">
						<div class="step-fields js-step-fields">
							@include('components.form-fields.loan-request', [
								'style' => 'bordered',
								'title' => 'Loan Request'
							])
						</div>

						<div class="step-fields js-step-fields">
							@include('components.form-fields', [
								'style' => 'bordered',
								'title' => 'Create Account',
								'fields' => [
									[
										'width' => 'half',
										'label' => 'Email',
										'input' => [
											'type' => 'email'
										]
									],
									[
										'width' => 'half',
										'label' => 'Confirm Email',
										'input' => [
											'type' => 'email',
											'name' => 'email_confirmation'
										]
									],
									[
										'width' => 'half',
										'label' => 'Password',
										'input' => [
											'type' => 'password',
											'autocomplete' => 'new-password'
										]
									],
									[
										'width' => 'half',
										'label' => 'Confirm Password',
										'input' => [
											'type' => 'password',
											'name' => 'password_confirmation'
										]
									]
								]
							])
						</div>

						<div class="actions">
							@include('components.button', [
								'tag' => 'button',
								'title' => 'Next Step: Business Information'
							])
						</div>
					</fieldset>

					<fieldset class="swiper-slide js-swiper-slide" data-title="Business Information">
						<div class="step-fields js-step-fields">
							@include('components.form-fields.business', [
								'style' => 'bordered'
							])
						</div>

						<div class="actions">
							<button
								type="button"
								class="button-prev js-button-prev">
								Go Back
							</button>

							@include('components.button', [
								'tag' => 'button',
								'title' => 'Next Step: Owner Information'
							])
						</div>
					</fieldset>

					<fieldset class="swiper-slide js-swiper-slide" data-title="Owner Information">
						<div class="step-fields js-step-fields">
							@include('components.form-fields.personal', [
								'style' => 'bordered',
								'titlePartners' => 'Partner Information',
								'showPhoto' => true
							])
						</div>

						<div class="actions">
							<button
								type="button"
								class="button-prev js-button-prev">
								Go Back
							</button>

							@include('components.button', [
								'tag' => 'button',
								'title' => 'Submit Application'
							])
						</div>
					</fieldset>

					{{-- <fieldset class="swiper-slide js-swiper-slide" data-title="Loan Request">
						<div class="actions">
							<button
								type="button"
								class="button-prev js-button-prev">
								Go Back
							</button>

							@include('components.button', [
								'tag' => 'button',
								'title' => 'Submit Application'
							])
						</div>
					</fieldset> --}}
				</div>
			</div>
		</form>
	</div>
@endsection
