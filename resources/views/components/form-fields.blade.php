@php
	// Fieldset attributes.
	$title = $title ?? '';
	$group = $group ?? '';
	$disabled = $disabled ?? false;
	$fieldset = $fieldset ?? true;
	$defaults = $defaults ?? collect([]);
	$locked = $locked ?? (isset($defaults->deleted_at) ? true : false);
@endphp

@if ($fieldset)
	<fieldset
		class="form-fields {{ prepareStyles($style ?? null) }} {{ $class ?? '' }} js-form-fields"
		@if ($disabled)
			disabled
		@endif>
@endif

	@if ($title)
		<span class="title text-h4 js-title">{{ $title }}</span>
	@endif

	@foreach ($fields as $field)
		@php
			// Default field attributes.
			$field['attrs'] = $field['attrs'] ?? [];
			$field['attrs']['disabled'] = $field['attrs']['disabled']
				?? $field['attrs']['hidden']
				?? false;

			// Default input attributes.
			$field['input']['class'] = $field['input']['class'] ?? '';
			$field['input']['disabled'] = $locked
				? true
				: ($field['input']['disabled'] ?? false);
			$field['input']['required'] = $locked || $field['input']['disabled']
				? false
				: ($field['input']['required'] ?? true);
			$field['type'] = $field['type'] ?? 'basic';
			$field['readonly'] = $field['readonly'] ?? false;
			$field['mask'] = $field['mask'] ?? false;
			$field['address'] = $field['address'] ?? false;
			$field['labelStyle'] = $field['labelStyle'] ?? [];

			// Append readonly class.
			if ($field['readonly']) {
				$field['input']['class'] .= ' read-only';
			}

			// Append input mask classes.
			if ($field['mask']) {
				$field['input']['class'] .= ' js-mask-' . $field['mask'];
			}

			// Append input address class.
			if ($field['address']) {
				$field['input']['class'] .= ' js-input-address';
			}

			// Slugify the label and use it if no name was provided.
			$name = $field['input']['name']
				?? (string) Str::of($field['label'] ?? '')
						->lower()
						->replace(' ', '_');

			// Group the field when needed.
			if ($group) {
				$name = $group . "[{$name}]";
			}

			// Append name to attributes array.
			$field['input']['name'] = $name;

			// Transform the name to dot notation.
			$nameDotNotation = (string) Str::of($name)
				->replace('[', '.')
				->replace(']', '');

			// Default values preference.
			$field['oldValue'] = old($nameDotNotation);
			$field['defaultValue'] = $field['oldValue']
				?? data_get($defaults, $nameDotNotation)
				?? $field['defaultValue']
				?? false;

			// Append type and default value to attributes array.
			if ($field['type'] === 'basic' || $field['type'] === 'signature' || $field['type'] === 'checkbox') {
				$field['input']['value'] = $field['defaultValue'];
			}

			// Append default number of rows to textarea.
			if ($field['type'] === 'multiline') {
				$field['input']['rows'] = $field['input']['rows'] ?? '4';
			}

			// Append the error class when needed.
			if($errors->has($nameDotNotation)) {
				$field['input']['class'] .= ' input-error';
			}

			// Prepare the field classes.
			$fieldClasses = collect([
				$field['width'] ?? '',
				$field['class'] ?? '',
				'field-' . $field['type']
			])->filter()->join(' ');
		@endphp

		<fieldset
			class="form-field {{ $fieldClasses }}"
			{{ renderAttrs($field['attrs']) }}>

			@isset ($field['label'])
				<label class="{{prepareStyles($field['labelStyle'])}}">
					{{ $field['label'] }} {{ $field['input']['required'] ? '*' : '' }}

					@isset ($field['labelSmall'])
						<small>{!! $field['labelSmall'] !!}</small>
					@endisset
				</label>
			@endisset

			<div class="input-wrapper">
				@if (isset($field['prepend']) && $field['prepend'])
					<div class="prepend">{{ $field['prepend'] }}</div>
				@endif

				@if (isset($field['append']) && $field['append'])
					<div class="append">{{ $field['append'] }}</div>
				@endif

				@include('components.input-' . $field['type'], $field)
			</div>

			@isset ($field['labelAfter'])
				<div class="label-after">{{ $field['labelAfter'] }}</div>
			@endisset
		</fieldset>
	@endforeach

@if ($fieldset)
	</fieldset>
@endif
