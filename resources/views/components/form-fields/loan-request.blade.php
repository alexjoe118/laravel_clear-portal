@php
	$defaults = $defaults ?? [];
	$partners = $defaults['partners'] ?? false;
	$fields = [
		[
			'width' => 'half',
			'label' => 'Loan Product',
			'type' => 'select',
			'options' => $selectOptions['loanProducts'],
			'input' => [
				'name' => 'loan_product_id',
				'class' => 'js-loan-product',
				'autofocus' => true
			]
		],
		[
			'width' => 'half',
			'label' => 'Amount Requested',
			'mask' => 'currency-integer',
			'prepend' => '$',
			'input' => [
				'name' => 'requested_amount'
			]
		],
		[
			'width' => 'third',
			'label' => 'When are funds needed?',
			'type' => 'select',
			'options' => $selectOptions['fundsNeededEstimates'],
			'input' => [
				'name' => 'funds_needed_estimate',
			]
		],
		[
			'width' => 'third',
			'label' => 'Use of funds',
			'type' => 'select',
			'options' => $selectOptions['fundsUsages'],
			'input' => [
				'name' => 'funds_usage',
			]
		],
		[
			'width' => 'third',
			'label' => 'Preferred method of contact',
			'type' => 'select',
			'options' => $selectOptions['communicationChannels'],
			'input' => [
				'name' => 'communication_channel'
			]
		],
		[
			'width' => 'full',
			'label' => 'Would you also like to see working capital options?',
			'class' => 'js-interest-in-wco',
			'defaultValue' => 1,
			'attrs' => [
				'hidden' => in_array($defaults['loan_product_id'] ?? [], [1, 2])
			],
			'type' => 'checkbox',
			'input' => [
				'name' => 'interest_in_working_capital_options'
			]
		]
	];

	// Append the Document Sets fields.
	$count = 0;
	foreach (App\Models\LoanProduct::all() as $loanProduct) {
		if ($loanProduct->required_document_sets_data || $loanProduct->required_document_types_data) {
			$documentSets = $loanProduct->required_document_sets_data ?? [];

			if ($documentTypes = $loanProduct->required_document_types_data) {
				array_unshift($documentSets, [
					'documentTypes' => $documentTypes
				]);
			}

			foreach ($documentSets as $documentSet) {
				if (isset($documentSet['title'])) {
					$fields[] = [
						'width' => 'full',
						'type' => 'title',
						'title' => $documentSet['title'],
						'style' => ['no-margin', 'medium'],
						'class' => 'js-required-documents',
						'attrs' => [
							'data-loan-product' => $loanProduct->id
						]
					];
				}

				foreach ($documentSet['documentTypes'] as $documentType) {
					$existingDocument = Auth::check()
						? Auth::user()->documents->firstWhere('document_type_id', $documentType->id)
						: false;

					$fields[] = [
						'type' => 'upload-file',
						'label' => $documentType->title,
						'class' => 'js-required-documents',
						'files' => $existingDocument ? [$existingDocument->filename] : null,
						'downloadAction' => $existingDocument ? route('user.document.download', ['id' => $existingDocument->id]) : null,
						'input' => [
							'required' => false,
							'name' => "required_documents[$count]"
						],
						'attrs' => [
							'data-loan-product' => $loanProduct->id
						]
					];

					$fields[] = [
						'class' => 'hidden',
						'defaultValue' => $documentType->id,
						'input' => [
							'name' => "required_documents_type_id[$count]",
						]
					];

					if ($existingDocument) {
						$fields[] = [
							'class' => 'hidden',
							'defaultValue' => $existingDocument->id,
							'input' => [
								'name' => "required_documents_existing[$loanProduct->id][]",
							]
						];
					}

					$count++;
				}
			}
		}
	}

	// Used to create a break between fields.
	$fields[] = [
		'type' => 'title',
		'width' => 'full'
	];

	if ($defaults) {
		$fields = array_merge($fields, [
			[
				'width' => 'half',
				'label' => 'Owner Signature',
				'type' => 'signature',
				'input' => [
					'name' => 'signature'
				]
			],
			[
				'width' => 'half',
				'label' => 'Owner Name',
				'labelAfter' => date('m/d/Y'),
				'defaultValue' => $defaults['first_name'] . ' ' . $defaults['last_name'],
				'input' => [
					'disabled' => true
				]
			],
			[
				'width' => 'full',
				'type' => 'title',
				'title' => 'Applicant Authorization',
				'titleSmall' => 'Applicant authorizes us, our assigns, agents, banks or financial institutions to obtain an investigative or consumer report from a credit bureau or a credit agency and to investigate the references given on any other statement or data obtained from applicant.'
			]
		]);
	}

	if ($partners) {
		foreach ($partners as $index => $partner) {
			$fields = array_merge($fields, [
				[
					'width' => 'half',
					'label' => 'Partner Signature',
					'type' => 'signature',
					'input' => [
						'name' => "partners[$index][signature]",
					]
				],
				[
					'width' => 'half',
					'label' => 'Partner Name',
					'labelAfter' => date('m/d/Y'),
					'defaultValue' => $partner['first_name'] . ' ' . $partner['last_name'],
					'input' => [
						'disabled' => true
					]
				],
				[
					'width' => 'full',
					'type' => 'title',
					'title' => 'Applicant Authorization',
					'titleSmall' => 'Applicant authorizes us, our assigns, agents, banks or financial institutions to obtain an investigative or consumer report from a credit bureau or a credit agency and to investigate the references given on any other statement or data obtained from applicant.'
				]
			]);
		}
	}
@endphp

<div class="form-fields-loan-request js-form-fields-loan-request">
	@include('components.form-fields', [
		'title' => $title ?? '',
		'style' => $style ?? '',
		'fields' => $fields
	])
</div>
