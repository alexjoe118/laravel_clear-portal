@php
	$tag = $tag ?? 'a';
	$style = $style ?? 'primary';
	$attrs = $attrs ?? [];
	$icon = $icon ?? 'svg.arrow-right';
	$confirm = $confirm ?? false;
	$confirmFields = $confirmFields ?? false;

	if ($confirm) {
		$attrs['data-confirm'] = true;
	}
@endphp

<{{ $tag }}
	@if($tag === 'button')
		type="{{ $type ?? 'submit' }}"
	@endif
	@isset($id)
		id="{{ $id }}"
	@endisset
	class="button {{ prepareStyles($style) }} {{ $class ?? '' }} js-button"
	@isset($onclick)
		onClick="{{$onclick}}"
	@endisset
	@foreach($attrs as $attr => $value)
		{{ $attr }}="{{ $value }}"
	@endforeach
	@isset($url)
		href="{{ $url }}"
	@endisset
	@isset($target)
		target="{{ $target }}"

		@if($target === '_blank')
			rel="noopener noreferrer"
		@endif
	@endisset>

	<span class="button-wrapper">
		{{ $title ?? '' }}

		@if($icon)
			<span class="icon">@include($icon)</span>
		@endif
	</span>

	@if ($confirm)
		<div class="confirmation js-confirmation">
			<div class="message text-normal">
				<b>Are you sure you want to proceed?</b>
				<span>{!! $confirmMessage ?? '' !!}</span>
			</div>

			@if ($confirmFields)
				@include('components.form-fields', [
					'title' => null,
					'group' => null,
					'style' => 'bordered',
					'fields' => $confirmFields
				])
			@endif

			<div class="actions">
				<div class="confirmation-button js-confirmation-button">Yes</div>
				<div class="confirmation-button js-confirmation-button" data-close>No</div>
			</div>
		</div>
	@endif
</{{ $tag }}>
