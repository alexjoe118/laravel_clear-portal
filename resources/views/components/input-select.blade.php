@php
	$multiple = $input['multiple'] ?? false;
	$onlyitem = $input['onlyitem'] ?? false;
	$readonly = $readonly ?? false;
	$placeholder = $readonly || $input['disabled'] ? '' : ($input['placeholder'] ?? 'Please Select');
	unset($input['placeholder']);
	$options = prepareOptionsArray($options);

	// Prepare classes.
	$classes = ['input-select'];
	foreach(['readonly', 'disabled', 'multiple'] as $attr) {
		if (isset($input[$attr]) && $input[$attr]) {
			$classes[] = $attr;
		}
	}

	// Adjust name if it's a multiple select.
	if ($multiple) {
		$input['name'] .= '[]';
	}
@endphp

<div class="{{ join(' ', $classes) }}">
	<select
		{{ renderAttrs($input) }}
		@if ($multiple)
			size="{{ count($options) }}"
		@endif>

	@if(! $onlyitem)	
		@if (! $multiple)
			<option selected value="">{{ $placeholder }}</option>

			@if (! $input['required'])
				<option value="">None</option>
			@endif
		@endif
	@endif
		@foreach ($options as $option => $label)
			<option
				value="{{ $option }}"
				@if (
					($multiple && in_array($option, $defaultValue ? $defaultValue : [])) ||
					(! $multiple && $defaultValue == $option)
				)
					selected
				@endif>
				{{ $label }}
			</option>
		@endforeach
	</select>
</div>
