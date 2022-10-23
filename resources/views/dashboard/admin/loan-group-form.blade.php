@extends('dashboard.admin.resource-form', [
	'resourceType' => 'Loan Group',
	'resource' => $loanGroup
])

@section('inputs')
	@include('components.form-fields', [
		'defaults' => $loanGroup,
		'fields' => [
			[
				'width' => 'full',
				'label' => 'Title'
			]
		]
	])
@endsection
