@if (session()->has('status') || session()->has('message'))
	<div class="form-success js-form-messages">
		{{ session()->get('status') ?? session()->get('message') }}
	</div>
@elseif ($errors->any())
	<div class="form-errors js-form-messages">
		<ul>
			@foreach ($errors->all() as $error)
				<li class="text-small">{!! $error !!}</li>
			@endforeach
		</ul>
	</div>
@endif
