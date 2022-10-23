@extends('dashboard.admin.resource-form', [
	'resourceType' => 'Open Approval',
	'resource' => $openApproval
])

@section('inputs')
	@if ($openApproval)
		@include('components.preview-options', [
			'options' => ['open-approval' => 'Preview'],
			'userId' => $openApproval->user->id
		])
	@endif

	@php
		$fields = [
			[
				'width' => 'half',
				'label' => 'User',
				'type' => 'select',
				'options' => $users,
				'defaultValue' => $userId,
				'readonly' => boolval($userId),
				'input' => [
					'name' => 'user_id',
				]
			],
			[
				'width' => 'half',
				'label' => 'Loan Product',
				'type' => 'select',
				'options' => $selectOptions['loanProducts'],
				'defaultValue' => $loanProductId,
				'input' => [
					'name' => 'loan_product_id',
					'class' => 'js-loan-product',
				]
			],
			[
				'width' => 'half',
				'label' => 'Loan Request',
				'type' => 'select',
				'options' => $loanRequests,
				'defaultValue' => $loanRequestId,
				'readonly' => boolval($loanRequestId),
				'input' => [
					'name' => 'loan_request_id',
				]
			],
			[
				'width' => 'half',
				'label' => 'Approval Expires',
				'mask' => 'date',
				'input' => [
					'required' => false
				]
			],
			[
				'width' => 'full',
				'label' => 'Notes',
				'type' => 'multiline',
				'input' => [
					'required' => false
				]
			],
			[
				'label' => 'Term Length',
				'append' => 'Months',
				'class' => 'js-term-length',
				'input' => [
					'type' => 'number',
					'min' => 1
				]
			],
			[
				'label' => 'Term Length Display',
				'type' => 'button-select',
				'class' => 'js-term-length-display',
				'options' => [
					'months' => 'Month(s)',
					'years' => 'Year(s)'
				]
			],
			[
				'label' => 'Total Credit Limit',
				'prepend' => '$',
				'mask' => 'currency',
				'class' => 'js-total-credit-limit'
			],
			[
				'label' => 'Maximum Amount',
				'prepend' => '$',
				'mask' => 'currency',
				'class' => 'js-maximum-amount'
			],
			[
				'label' => 'Rate',
				'append' => '%',
				'class' => 'js-rate',
				'input' => [
					'type' => 'number',
					'min' => 0,
					'max' => 100,
					'step' => 'any'
				]
			],
			[
				'label' => 'Interest Rate',
				'append' => '%',
				'class' => 'js-interest-rate',
				'input' => [
					'type' => 'number',
					'min' => 0,
					'max' => 100,
					'step' => 'any'
				]
			],
			[
				'label' => 'Weekly Rate',
				'append' => '%',
				'class' => 'js-factor-rate',
				'input' => [
					'type' => 'number',
					'min' => 0,
					'max' => 100,
					'step' => 'any',
					'name' => 'factor_rate'
				]
			],
			[
				'label' => 'Draw Fee',
				'append' => '%',
				'class' => 'js-draw-fee',
				'input' => [
					'type' => 'number',
					'min' => 0,
					'max' => 100,
					'step' => 'any'
				]
			],
			[
				'label' => 'Misc Fees',
				'prepend' => '$',
				'mask' => 'currency',
				'class' => 'js-misc-fees'
			],
			[
				'label' => 'Multiplier',
				'append' => '%',
				'class' => 'js-multiplier',
				'input' => [
					'type' => 'number',
					'min' => 0,
					'max' => 100,
					'step' => 'any'
				]
			],
			[
				'label' => 'Closing Costs',
				'append' => '%',
				'class' => 'js-closing-costs',
				'input' => [
					'type' => 'number',
					'min' => 0,
					'step' => 'any',
					'required' => false
				]
			],
			[
				'label' => 'Closing Costs Display',
				'type' => 'button-select',
				'class' => 'js-closing-costs-display',
				'options' => [
					'percentage' => '%',
					'dollars' => '$',
					'waived' => 'Waived'
				]
			],
			[
				'label' => 'Cost of Capital',
				'append' => '%/month',
				'class' => 'js-cost-of-capital',
				'input' => [
					'type' => 'number',
					'min' => 0,
					'max' => 100,
					'step' => 'any',
					'required' => false
				]
			],
			[
				'label' => 'Show Cost of Capital',
				'type' => 'checkbox',
				'class' => 'js-cost-of-capital-display',
				'input' => [
					'name' => 'cost_of_capital_display'
				]
			],
			[
				'label' => 'Payment Frequency',
				'type' => 'select',
				'options' => [
					'daily' => 'Daily',
					'weekly' => 'Weekly',
					'semi-monthly' => 'Semi-Monthly',
					'monthly' => 'Monthly'
				],
				'class' => 'js-payment-frequency-1'
			],
			[
				'label' => 'Payment Frequency',
				'type' => 'select',
				'options' => [
					'monthly' => 'Monthly'
				],
				'class' => 'js-payment-frequency-2'
			],
			[
				'label' => 'Payment Frequency',
				'type' => 'select',
				'options' => [
					'weekly' => 'Weekly',
					'semi-monthly' => 'Semi-Monthly',
					'monthly' => 'Monthly'
				],
				'class' => 'js-payment-frequency-3'
			],
			[
				'label' => 'Number of Payments',
				'class' => 'js-number-of-payments',
				'input' => [
					'type' => 'number',
					'min' => 1
				]
			],
			[
				'label' => 'Maximum Advance',
				'prepend' => '$',
				'mask' => 'currency',
				'class' => 'js-maximum-advance'
			],
			[
				'label' => 'Prepayment Discount',
				'type' => 'checkbox',
				'class' => 'js-prepayment-discount'
			]
		];
	@endphp

	@include('components.form-fields', [
		'defaults' => $openApproval,
		'fields' => $fields
	])
@endsection

