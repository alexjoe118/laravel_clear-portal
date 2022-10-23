@php
	$columns = $columns ?? 3;
	$slider = $slider ?? false;
@endphp

<div class="grid-boxes js-grid-boxes">
	@isset($title)
		@include('components.page-subtitle', ['subtitle' => $title])
	@endisset

	@include('components.swiper-controls')

	<div
		class="{{ $slider ? 'swiper js-swiper' : '' }}"
		@if ($slider)
			data-slides-per-view="{{ $columns }}"
		@endif>
		<div class="grid cols-{{ $columns }} {{ $slider ? 'swiper-wrapper' : '' }}">
			@php
				$i = 1;
			@endphp

			@while(true)
				@isset(${'box_'. $i})
					<div class="box {{ $slider ? 'swiper-slide' : '' }} {{ $slideClass ?? '' }}">
						{{ ${'box_'. $i} }}
					</div>
				@else
					@break
				@endisset

				@php
					$i++;
				@endphp
			@endwhile
		</div>
	</div>
</div>
