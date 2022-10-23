@php
	$label = $label ?? '';
	$options = $options ?? [
		'open-approval' => 'Open Approvals',
		'loan' => 'Loans',
		'document' => 'Documents'
	];
@endphp

<div class="preview-options js-preview-options">
	@if ($label)
		<span>{{ $label }}</span>
	@endif

	@foreach ($options as $type => $label)
		@include('components.button', [
			'title' => $label,
			'id' => "modal-$type-trigger"
		])

		@component('components.modal', ['id' => "modal-$type"])
			<div class="iframe-wrapper js-iframe-wrapper">
				<iframe class="js-iframe" src="{{ route("admin.user.$type.index", ['id' => $userId]) }}">
				</iframe>
			</div>
		@endcomponent
	@endforeach
</div>
