@php
	$values = is_array($defaultValue) ? $defaultValue : [''];
	$input['name'] .= '[]';
@endphp

<div class="input-repeater js-input-repeater">
	@foreach($values as $value)
		<div class="input-wrapper js-input-wrapper">
			<input
				type="text"
				value="{{ $value }}"
				{{ renderAttrs($input) }} />

			<button
				type="button"
				class="button-remove js-button-remove">
				@include('svg.close')
			</button>
		</div>
	@endforeach

	@include('components.button', [
		'tag' => 'button',
		'type' => 'button',
		'title' => $buttonTitle ?? 'Add',
		'icon' => 'svg.plus-sign',
		'style' => 'secondary',
		'class' => 'js-repeater-button',
		'attrs' => []
	])
</div>
