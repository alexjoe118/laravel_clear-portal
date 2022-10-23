@php
	$toggler = $toggler ?? false;
	$actions = $actions ?? ['edit', 'delete'];
	$subItems = isset($slot) && $slot->isNotEmpty() ? $slot :  false;
@endphp

<div class="resource-listing-item js-item {{ $item->deleted_at ? 'deleted' : '' }}">
	<div class="item-header">
		@if ($subItems)
			<button class="toggler text-h5 js-toggler">{{ $title }}</button>
		@else
			<a
				href="@if (in_array('edit', $actions))
					{{ route("admin.$resource.edit", ['id' => $item->id]) }}
				@elseif (in_array('show', $actions))
					{{ route("admin.$resource.show", ['id' => $item->id]) }}
				@endif"
				class="title text-h5">{{
				getTitle($item)
			}}</a>
		@endif

		<div class="actions">
			@if (! $item->deleted_at)
				@if (in_array('notify', $actions) || array_key_exists('notify', $actions))
					<form
						method="POST"
						data-action="{{ route("admin.$resource.notify", ['id' => $item->id]) }}">
						@csrf

						@include('components.button', [
							'tag' => 'button',
							'title' => 'Notify',
							'confirm' => true,
							'confirmMessage' => $actions['notify']['confirmMessage']
								?? 'The user will receive an in-app and email notification about this ' . Str::headline($resource) . '.',
							'confirmFields' => $actions['notify']['confirmFields'] ?? null
						])
					</form>
				@endif

				@if (in_array('show', $actions) || array_key_exists('show', $actions))
					@include('components.button', [
						'title' => 'Details',
						'url' => route("admin.$resource.show", ['id' => $item->id])
					])
				@endif

				@if (in_array('edit', $actions) || array_key_exists('edit', $actions))
					@include('components.button', [
						'title' => 'Edit',
						'url' => route("admin.$resource.edit", ['id' => $item->id])
					])
				@endif

				@if (in_array('delete', $actions) || array_key_exists('delete', $actions))
					<form
						method="POST"
						data-action="{{ route("admin.$resource.destroy", ['id' => $item->id]) }}">
						@csrf
						@method('DELETE')

						@include('components.button', [
							'tag' => 'button',
							'title' => 'Delete',
							'style' => 'secondary',
							'icon' => 'svg.thrash',
							'confirm' => true,
							'confirmMessage' => $actions['delete']['confirmMessage'] ?? null,
							'confirmFields' => $actions['delete']['confirmFields'] ?? null
						])
					</form>
				@endif
			@elseif (in_array('delete', $actions) || array_key_exists('delete', $actions))
				<form
					method="POST"
					data-action="{{ route("admin.$resource.restore", ['id' => $item->id]) }}">
					@csrf

					@include('components.button', [
						'tag' => 'button',
						'title' => 'Restore',
						'style' => 'secondary',
						'icon' => 'svg.refresh',
						'confirm' => true
					])
				</form>
			@endif
		</div>
	</div>

	@if ($subItems)
		<div class="item-content js-item-content">
			<div class="item-content-wrapper js-item-content-wrapper">
				{{ $subItems }}
			</div>
		</div>
	@endif
</div>
