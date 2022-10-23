<picture class="picture {{ $class ?? '' }}">
	@isset($link)
		<a
			href="{{ $link['url'] }}"
			title="{{ $link['title'] ?? 'Learn more' }}"
			aria-label="{{ $link['title'] ?? 'Learn more' }}"
			@if(isset($link['target']) && ($link['target'] === '_blank'))
				target="_blank"
				rel="noopener noreferrer"
			@endif
			class="link">
		</a>
	@endisset

	<img
		@isset($alt)
			alt="{{ $alt }}"
		@endisset
		src="{{ $src }}"
		class="img {{ $imgClass ?? '' }}"
	/>
</picture>
