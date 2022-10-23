@php
	$id = $id ?? false;
	$close = $close ?? true;
	$multipleTriggers = $multipleTriggers ?? false;
@endphp

<div
	@if ($id)
		id="{{ $id }}"

		@if (! $multipleTriggers)
			aria-labelledby="{{ $id }}-trigger"
		@endif
	@endif
	class="modal js-modal {{ $class ?? '' }} {{ $multipleTriggers ? 'multiple-triggers' : '' }}"
	role="dialog"
	aria-modal="true">

	<div class="modal-wrapper js-modal-wrapper">
		@if ($close)
			<button
				type="button"
				class="modal-close js-modal-close">
				@include('svg.close')
			</button>
		@endif

		{{ $slot }}
	</div>
</div>
