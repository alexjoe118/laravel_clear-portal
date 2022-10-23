<div class="listing-items">
	@php
		$i = 1;
	@endphp

	@while(true)
		@isset(${'item_'. $i})
			<div class="item">
				{{ ${'item_'. $i} }}
			</div>
		@else
			@break
		@endisset

		@php
			$i++;
		@endphp
	@endwhile
</div>
