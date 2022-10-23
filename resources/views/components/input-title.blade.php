@php
	$line = $line ?? false;
	$style = $style ?? [];
@endphp

<div class="input-title {{ prepareStyles($style) }}">
	<h3>{{ $field['title'] ?? '' }}</h3>

	@isset ($field['titleSmall'])
		<small>{!! $field['titleSmall'] !!}</small>
	@endisset
</div>
