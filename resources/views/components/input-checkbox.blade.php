@php
	$defaultValue = $defaultValue ? 1 : 0;
	$input['required'] = false;
	$name = $input['name'];
	unset($input['name']);
@endphp

<div class="input-checkbox js-input-checkbox">
	<input
		type="hidden"
		value="{{ $defaultValue }}"
		name="{{ $name }}"
		class="js-input-value" />

	<input
		type="checkbox"
		class="input js-input"
		{{ renderAttrs($input) }}
		@if ($defaultValue)
			checked
		@endif />

	<div class="toggle">
		<span class="label">Yes</span>
		<span class="label">No</span>
	</div>
</div>
