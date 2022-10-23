@extends('dashboard.admin.resource-form', [
	'resourceType' => 'Loan',
	'resource' => $loan
])

@section('inputs')
	@if ($loan->openApproval)
		@include('components.button', [
			'url' => route('admin.open-approval.edit', ['id' => $loan->openApproval->id]),
			'title' => 'Open Approval',
			'class' => 'open-approval-link'
		])
	@endif

	@if ($loan->user)
		@include('components.preview-options', [
			'options' => ['loan' => 'Preview'],
			'userId' => $loan->user->id
		])
	@endif

	@php
		$fields = [
			[
				'width' => 'half',
				'label' => 'User',
				'input' => [
					'name' => 'user[full_name]',
					'disabled' => true
				]
			],
			[
				'width' => 'half',
				'label' => 'Loan Product',
				'input' => [
					'name' => 'loan_product[title]',
					'disabled' => true,
				]
			]
		];

		if ($loan->loan_product_id !== 9) {
			$fields = array_merge($fields, [
				[
					'label' => 'Term Length',
					'append' => 'Months'
				],
				[
					'label' => 'Term Length Display',
					'type' => 'button-select',
					'options' => [
						'months' => 'Month(s)',
						'years' => 'Year(s)'
					]
				],
			]);
		}

		$fields[] = [
			'label' => $loan->loan_amount ? 'Loan Amount' : 'Credit Limit',
			'mask' => 'currency',
			'prepend' => '$'
		];

		if (! in_array($loan->loan_product_id, [8, 9])) {
			$fields = array_merge($fields, [
				[
					'label' => 'Payback Amount',
					'mask' => 'currency',
					'prepend' => '$'
				],
				[
					'label' => 'Payment Amount',
					'mask' => 'currency',
					'prepend' => '$'
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
					'input' => [
						'class' => 'js-payment-frequency'
					]
				],
				[
					'label' => 'Payment Day',
					'type' => 'select',
					'class' => 'js-payment-day-monthly',
					'attrs' => [
						'hidden' =>  $loan->payment_frequency !== 'monthly'
					],
					'options' => [
						'1' => '1st',
						'5' => '5th',
						'10' => '10th',
						'15' => '15th',
						'20' => '20th',
						'25' => '25th',
						'last' => 'Last'
					]
				],
				[
					'label' => 'Payment Day',
					'type' => 'select',
					'class' => 'js-payment-day-semi-monthly',
					'attrs' => [
						'hidden' => $loan->payment_frequency !== 'semi-monthly'
					],
					'options' => [
						'1' => '1st',
						'15' => '15th'
					]
				],
				[
					'label' => 'Payment Day',
					'type' => 'select',
					'class' => 'js-payment-day-weekly',
					'attrs' => [
						'hidden' => $loan->payment_frequency !== 'weekly'
					],
					'options' => [
						'Monday',
						'Tuesday',
						'Wednesday',
						'Thursday',
						'Friday'
					]
				],
				[
					'label' => 'Number of Payments',
					'class' => 'js-number-of-payments',
					'input' => [
						'type' => 'number',
						'min' => 1
					]
				]
			]);
		}

		$fields[] = [
			'label' => 'Lender',
			'type' => 'select',
			'options' => $lenders,
			'input' => [
				'name' => 'lender_id'
			]
		];

		if (in_array($loan->loan_product_id, [1, 2])) {
			$fields[] = [
				'label' => 'Estimated Renewal Date',
				'mask' => 'date'
			];
		}

		if (! in_array($loan->loan_product_id, [8, 9])) {
			$fields = array_merge($fields, [
				[
					'label' => 'Payoff Date',
					'mask' => 'date',
					'input' => [
						'required' => false
					]
				],
				[
					'label' => 'Estimated Payoff Date',
					'mask' => 'date'
			],
			[
				'width' => 'full',
				'type' => 'upload-file',
				'label' => 'Contract Documents',
				'labelAllowedFiles' => 'PDF',
				'class' => 'js-required-documents',
				'enqueued' => true,
				'files' => $loan->contract_documents,
				'deleteAction' => $loan->contract_documents ? route('admin.loan.delete-contract-documents', ['id' => $loan->id]) : false,
				'downloadAction' => $loan->contract_documents ? route('admin.loan.download-contract-documents', ['id' => $loan->id]) : false,
				'input' => [
					'required' => false,
					'accept' => '.pdf',
					'multiple' => true
				],
			]
			]);
		}

		$fields = array_merge($fields, [
			[
				'width' => 'full',
				'label' => 'Funded',
				'labelSmall' => 'When the Loan is marked as funded, the Funded Date will default to the current day, unless it\'s filled with a different date.',
				'type' => 'checkbox'
			],
			[
				'label' => 'Funded Date',
				'mask' => 'date',
				'input' => [
					'required' => false,
					'disabled' => $loan->funded
				]
			],
		]);

		if ($loan->funded) {
			$fields = array_merge($fields, [
				[
					'width' => 'full',
					'type' => 'checkbox',
					'label' => 'Default',
					'labelSmall' => 'When selected, users will not be able to see Estimated Remaining Balance, Estimated Renewal Date, Estimated Payoff Date and buttons when visualizing this Loan.'
				]
			]);
		}
	@endphp

	@if ($loan->funded)
		<div class="alert">This loan was funded already. You probably shouldn't edit its key informations from now on.</div>
	@endif

	@include('components.form-fields', [
		'defaults' => $loan,
		'fields' => $fields
	])
@endsection
