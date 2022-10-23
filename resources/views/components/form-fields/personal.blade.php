@php
	$defaults = $defaults ?? false;
	$partnersTitle = $partnersTitle ?? false;
	$partners = old('partners') ?? ($defaults && $defaults->partners->isNotEmpty() ? $defaults->partners->toArray() : [false]);
	$showPhoto = $showPhoto ?? false;
	$locked = $locked ?? false;

	$fields = [
		[
			'width' => 'half',
			'label' => 'First Name'
		],
		[
			'width' => 'half',
			'label' => 'Last Name'
		],
		[
			'width' => 'third',
			'label' => 'Phone Number',
			'mask' => 'phone-number'
		],
		[
			'width' => 'third',
			'label' => 'Title',
			'input' => [
				'required' => false
			]
		],
		[
			'width' => 'third',
			'label' => 'Date of Birth',
			'mask' => 'date'
		],
		[
			'label' => 'Address Line 1',
			'address' => true
		],
		[
			'label' => 'Address Line 2',
			'input' => [
				'required' => false,
			]
		],
		[
			'label' => 'City'
		],
		[
			'label' => 'State',
			'type' => 'select',
			'options' => $selectOptions['states']
		],
		[
			'label' => 'ZIP Code',
			'mask' => 'zip-code'
		],
		'ssn' => [
			'label' => 'SSN',
			'mask' => 'ssn'
		],
		[
			'label' => 'Approximate Credit Score',
			'type' => 'select',
			'options' => $selectOptions['approximateCreditScores'],
		],
		'business_ownership' => [
			'label' => 'Business Ownership',
			'append' => '%',
			'input' => [
				'type' => 'number',
				'min' => 0,
				'max' => 100,
				'step' => 'any'
			]
		],
		'signature' => [
			'width' => 'half',
			'label' => 'Owner Signature',
			'type' => 'signature',
			'input' => [
				'name' => 'signature'
			]
		],
		[
			'width' => 'full',
			'type' => 'title',
			'title' => 'Applicant Authorization',
			'titleSmall' => 'Applicant authorizes us, our assigns, agents, banks or financial institutions to obtain an investigative or consumer report from a credit bureau or a credit agency and to investigate the references given on any other statement or data obtained from applicant.'
		]
	];
@endphp

<div class="form-fields-personal js-form-fields-personal">

	{{-- User Information --}}
	@php
		$userFields = $fields;

		$userFields['ssn']['readonly'] = $defaults ? Str::contains($defaults['ssn'], '*') : false;
		$userFields['business_ownership']['input']['class'] = 'js-ownership';
		$userFields['business_ownership']['defaultValue'] = '100';

		if ($showPhoto) {
			$userFields = array_merge([[
				'width' => 'full',
				'type' => 'upload-image',
				'style' => 'large',
				'input' => [
					'name' => 'photo',
					'required' => false
				]
			]], $userFields);
		}
	@endphp

	@include('components.form-fields', [
		'title' => $title ?? '',
		'style' => $style ?? '',
		'class' => $class ?? '',
		'defaults' => $defaults,
		'disabled' => $disabled ?? '',
		'fields' => $userFields
	])

	{{-- Partner(s) Information --}}
	@foreach ($partners as $index => $partner)
		@php
			$class = 'partner js-partner';
			$partnerFields = $fields;
			$partnerFields['ssn']['readonly'] = $partner ? Str::contains($partner['ssn'], '*') : false;
			$partnerFields['signature']['label'] = 'Partner Signature';

			if ($partner) {
				$class .= ' active';

				$partnerFields[] = [
					'input' => [
						'type' => 'hidden',
						'name' => 'id'
					]
				];
			}

			if ($index > 0 && ! $locked) {
				$class .= ' removable';
			}
		@endphp

		@include('components.form-fields', [
			'title' => $titlePartners ?? '',
			'style' => $style ?? '',
			'class' => $class,
			'group' => "partners[$loop->index]",
			'defaults' => $defaults,
			'disabled' => ! $partner,
			'fields' => $partnerFields
		])
	@endforeach

	@if (! $locked)
		@include('components.button', [
			'tag' => 'button',
			'type' => 'button',
			'title' => 'Add Partner',
			'class' => 'js-add-partner',
			'style' => 'secondary',
			'icon' => 'svg.plus-sign'
		])
	@endif
</div>
