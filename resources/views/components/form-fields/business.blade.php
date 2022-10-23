<div class="form-fields-business">
	@include('components.form-fields', [
		'title' => $title ?? '',
		'style' => $style ?? '',
		'group' => 'business',
		'fields' => [
			[
				'label' => 'Legal/Corporate Name',
				'input' => [
					'name' => 'name'
				]
			],
			[
				'label' => 'DBA',
				'input' => [
					'required' => false
				]
			],
			[
				'label' => 'Address Line 1',
				'address' => true
			],
			[
				'label' => 'Address Line 2',
				'input' => [
					'required' => false
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
			[
				'label' => 'Phone Number',
				'mask' => 'phone-number'
			],
			[
				'label' => 'Federal Tax ID',
				'mask' => 'federal-tax-id'
			],
			[
				'label' => 'Business Start Date',
				'mask' => 'date',
				'input' => [
					'name' => 'start_date'
				]
			],
			[
				'label' => 'Website',
				'input' => [
					'type' => 'text',
					'placeholder' => 'e.g. https://website.com',
					'required' => false
				]
			],
			[
				'label' => 'Type of Entity',
				'type' => 'select',
				'options' => $selectOptions['typesOfEntities']
			],
			[
				'label' => 'Industry',
				'type' => 'select',
				'options' => $selectOptions['industries']
			],
			[
				'label' => 'Gross Annual Sales',
				'type' => 'select',
				'options' => $selectOptions['grossAnnualSales']
			],
			[
				'label' => 'Monthly Sales Volume',
				'type' => 'select',
				'options' => $selectOptions['monthlySalesVolumes']
			]
		]
	])
</div>
