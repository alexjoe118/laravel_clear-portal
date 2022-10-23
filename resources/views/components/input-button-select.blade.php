@php
	$options = prepareOptionsArray($options);
@endphp

<div class="input-button-select">
	@foreach ($options as $option => $label)
		<button>
			<input
				type="radio"
				value="{{ $option }}"
				{{ renderAttrs($input) }}
				@if (($defaultValue && $defaultValue === $option) || $loop->first)
					checked
				@endif />
			<label>{{ $label }}</label>
		</button>
	@endforeach
</div>
