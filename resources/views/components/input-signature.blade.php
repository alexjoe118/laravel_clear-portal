@php
	$disabled = $oldValue ? false : boolval($defaultValue);
@endphp

<div class="input-signature js-input-signature">
	<input
		type="{{ $disabled ? 'hidden' : 'text' }}"
		class="input js-input"
		{{ renderAttrs($input) }}
		@if ($disabled)
			disabled
		@endif />

	<canvas></canvas>

	<div class="actions">
		<button type="button" class="button-clear js-button-clear">Clear</button>
	</div>
</div>
