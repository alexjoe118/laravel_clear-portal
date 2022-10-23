@extends('dashboard.admin.resource-form', [
	'resourceType' => 'Loan Request',
	'resource' => $loanRequest
])

@section('button-extra')
	@include('components.button', [
		'title' => 'Create Approval',
		'style' => 'outline-primary',
		'url' => route('admin.open-approval.create', [
			'user_id' => $loanRequest->user->id,
			'loan_product_id' => $loanRequest->loan_product_id,
			'loan_request_id' => $loanRequest->id
		])
	])
@endsection

@section('inputs')
	@php
		$fields = [
			[
				'width' => 'third',
				'label' => 'User',
				'input' => [
					'name' => 'user[business[name]]',
					'disabled' => true
				]
			],
			[
				'width' => 'third',
				'label' => 'Loan Product',
				'input' => [
					'name' => 'loanProduct[title]',
					'disabled' => true
				]
			],
			[
				'width' => 'third',
				'label' => 'Amount Requested',
				'mask' => 'currency',
				'prepend' => '$',
				'input' => [
					'name' => 'requested_amount',
					'disabled' => true
				]
			],
			[
				'width' => 'third',
				'label' => 'When are funds needed?',
				'type' => 'select',
				'options' => $selectOptions['fundsNeededEstimates'],
				'input' => [
					'name' => 'funds_needed_estimate',
					'disabled' => true
				]
			],
			[
				'width' => 'third',
				'label' => 'Use of funds',
				'type' => 'select',
				'options' => $selectOptions['fundsUsages'],
				'input' => [
					'name' => 'funds_usage',
					'disabled' => true
				]
			],
			[
				'width' => 'third',
				'label' => 'Preferred method of contact',
				'type' => 'select',
				'options' => $selectOptions['communicationChannels'],
				'input' => [
					'name' => 'communication_channel',
					'disabled' => true
				]
			]
		];

		if (! in_array($loanRequest->loanProduct->id, [1, 2])) {
			$fields[] = [
				'width' => 'full',
				'label' => 'Interest in working capital options',
				'type' => 'checkbox',
				'input' => [
					'name' => 'interest_in_working_capital_options',
					'disabled' => true
				]
			];
		}

		$fields = array_merge($fields, [
			[
				'width' => 'half',
				'label' => 'Owner Signature',
				'type' => 'signature',
				'input' => [
					'name' => 'user[signature]',
					'disabled' => true
				]
			],
			[
				'width' => 'half',
				'label' => 'Owner Name',
				'input' => [
					'name' => 'user[full_name]',
					'disabled' => true
				]
			],
			[
				'width' => 'full',
				'label' => 'Open Approval Notes',
				'type' => 'multiline',
				'labelSmall' => 'Fill this note in order to use in all open approvals.',
				'input' => [
					'required' => false
				]
			]
		]);

		if ($loanRequest->user->partners->isNotEmpty()) {
			foreach ($loanRequest->user->partners as $index => $partner) {
				$fields[] = [
					'width' => 'half',
					'label' => 'Partner Signature',
					'type' => 'signature',
					'input' => [
						'name' => "user[partners][$index][signature]"
					]
				];

				$fields[] = [
					'width' => 'half',
					'label' => 'Partner Name',
					'input' => [
						'name' => "user[partners][$index][full_name]"
					]
				];
			}
		}
	@endphp

	@include('components.form-fields', [
		'defaults' => $loanRequest,
		'style' => 'bordered',
		'fields' => $fields
	])

	@if ($loanRequest->documents)
		<div class="documents">
			@foreach ($loanRequest->documents as $document)
				@if	($document->document_type_id)
					<span>{{ App\Models\DocumentType::find($document->document_type_id)->title }}</span>
				@else
					<span>Uncategorized</span>
				@endif

				@include('components.file', [
					'filename' => $document->filename,
					'downloadAction' => route('admin.document.download', ['id' => $document->id])
				])
			@endforeach
		</div>
	@endif
@endsection
