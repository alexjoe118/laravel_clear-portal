@php
	$accordion = $accordion ?? false;
	$resourceType = Str::between(Route::currentRouteName(), '.', '.');
@endphp

<div class="resource-listing js-resource-listing">
	@if ($pagination->isNotEmpty())
		{{ $slot }}

		@if ($pagination->previousPageUrl() || $pagination->nextPageUrl())
			<div class="pagination">
				@if ($pagination->previousPageUrl())
					<div>
						@include('components.button', [
							'icon' => 'svg.arrow-right',
							'url' => $pagination->previousPageUrl() . '&pagecount=' . $pagenum,
							'class' => 'arrow-prev'
						])
					</div>
				@endif

				@if ($pagination->nextPageUrl())
					<div>
						@include('components.button', [
							'icon' => 'svg.arrow-right',
							'url' => $pagination->nextPageUrl() . '&pagecount=' . $pagenum,
							'class' => 'arrow-next'
						])
					</div>
				@endif

				<span class="status">Page {{ $pagination->currentPage() }} of {{ $pagination->lastPage() }}</span>
			</div>
		@endif
	@else
		<div>There are no {{ Str::of($resourceType)->headline()->plural() }} yet.</div>
	@endif
</div>
