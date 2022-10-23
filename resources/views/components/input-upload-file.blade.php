@php
	$action = $action ?? false;
	$enqueued = $enqueued ?? false;
	$input['accept'] = $input['accept'] ?? '.pdf, .doc, .docx, .ppt, .jpg, .jpeg, .png, .zip, .xls';
	$input['name'] = $input['name'] . (isset($input['multiple']) ? '[]' : '');
	$input['disabled'] = $input['disabled'] ?? false;
	$classes = join(' ', [
		$enqueued ? 'enqueued' : '',
		$input['disabled'] ? 'disabled' : ''
	]);
@endphp

@if ($action)
	<form
		method="POST"
		action="{{ $action }}"
		enctype="multipart/form-data"
		class="input-upload-file js-input-upload-file {{ $classes }}">
		@csrf
@else
	<div class="input-upload-file js-input-upload-file {{ $classes }}">
@endif
	<div class="upload">
		<div class="icon">
			@include('svg.upload')
		</div>

		<div>
			<span class="title text-extra-small">Upload Files</span>
			<span class="allowed">{{ $labelAllowedFiles ?? 'PDF, DOC, PPT, JPG, PNG, XLS' }}</span>
		</div>

		<input
			type="file"
			class="input js-input"
			{{ renderAttrs($input) }} />

		@isset ($slot)
			{{ $slot }}
		@endisset
	</div>

	@include('components.file', [
		'filename' => '',
		'downloadAction' => false,
		'deleteAction' => false
	])

	@foreach ($files ?? [] as $file)
		@include('components.file', [
			'filename' => $file,
			'downloadAction' => $downloadAction ?? false
		])
	@endforeach
@if ($action)
	</form>
@else
	</div>
@endif
