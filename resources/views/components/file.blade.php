@php
	$downloadAction = $downloadAction ?? false;
@endphp

<div class="file js-file {{ empty($filename) ? 'hidden' : '' }}">
	<div class="icon">
		@include('svg.paper')
	</div>

	<div class="file-info">
		<span class="name text-extra-small js-file-name">{{ $filename }}</span>
		<span class="size text-caption js-file-size"></span>
	</div>

	@if ($downloadAction)
		<a
			href="{{ $downloadAction }}?filename={{ $filename }}"
			target="_blank"
			class="file-action">
			@include('svg.download')
		</a>
	@endif

	@isset ($deleteAction)
		<a
			@if ($deleteAction)
				href="{{ $deleteAction }}?filename={{ $filename }}"
			@endif
			class="file-action js-file-remove">
			@include('svg.close')
		</a>
	@endisset
</div>
