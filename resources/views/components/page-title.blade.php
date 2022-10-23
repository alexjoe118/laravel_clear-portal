<div class="page-title js-page-title">
	<div>
		@include('svg.leaf')
		<h2 class="text-h2">{{ $title }}</h2>
	</div>

	{{ $slot ?? '' }}
</div>
