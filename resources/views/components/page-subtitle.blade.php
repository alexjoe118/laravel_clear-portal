<span class="page-subtitle {{ $class ?? '' }} {{ isset($slot) && $slot->isNotEmpty() ? 'with-slot' : '' }}">
	@if($subtitle)
		<span>{{ $subtitle }}</span>
	@endif

	<div class="line" aria-hidden="true"></div>

	{{ $slot ?? '' }}
</span>
