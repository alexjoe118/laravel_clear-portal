@php
	$defaultValue = $defaultValue ? $defaultValue : asset('images/dashboard/user-placeholder.png');
	$style = $style ?? [];
	$input['name'] = $input['name'] ?? 'upload';
	$input['accept'] = $input['accept'] ?? '.png, .jpg, .jpeg, .svg';
@endphp

<div class="input-upload-image js-input-upload-image {{ prepareStyles($style) }}">
	<img class="preview js-preview" />

	@include('components.picture', [
		'src' => $defaultValue,
		'imgClass' => 'js-img'
	])

	<input
		type="file"
		class="input js-input"
		{{ renderAttrs($input) }}>
</div>
