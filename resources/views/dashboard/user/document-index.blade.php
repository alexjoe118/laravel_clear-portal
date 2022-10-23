@extends('layouts.dashboard', ['pageTitle' => 'My Documents'])

@section('page')
	@include('components.form-messages')

	@component('components.grid-boxes', [
		'columns' => 5
	])
		@foreach ($documentGroups as $documentGroup)
			@slot('box_'. $loop->iteration)
				<div class="document-group">
					<span class="title text-h4">{{ $documentGroup->title }}</span>

					<div class="documents js-documents text-small">
						@foreach($documentGroup->documents as $document)
							<div class="document js-document">
								<span>
									@if ($document->documentType)
										<span class="document-type">{{ $document->documentType->title }}</span>
									@endif

									<form
										method="POST"
										action="{{ route('user.document.update', ['id' => $document->id]) }}"
										class="select">
										@csrf
										@method('PUT')

										<input type="text" name="filename" value="{{ $document->filename }}" required />
									</form>
								</span>

								<div class="actions">
									<form
										method="POST"
										action="{{ route('user.document.update', ['id' => $document->id]) }}"
										class="select">
										@csrf
										@method('PUT')

										<select
											class="js-group-select"
											name="document_group_id">

											@foreach ($documentGroups as $g)
												@if ($g->id !== 0)
													<option
														value="{{ $g->id }}"
														@if ($g->id == $document->document_group_id)
															selected
														@endif>
														{{ $g->title }}
													</option>
												@endif
											@endforeach
										</select>

										@include('svg.push-pin')
									</form>

									<a
										href="{{ route('user.document.download', ['id' => $document->id]) }}"
										target="_blank" rel="noopener noreferrer">

										<button>@include('svg.download')</button>
									</a>

									<form
										method="POST"
										action="{{ route('user.document.destroy', ['id' => $document->id]) }}"
										class="delete">
										@csrf
										@method('DELETE')

										<button>@include('svg.thrash')</button>
									</form>
								</div>
							</div>
						@endforeach
					</div>

					@component('components.input-upload-file', [
						'action' => route('user.document.store'),
						'input' => [
							'name' => 'document'
						]
					])
						<input
							type="hidden"
							name="document_group_id"
							value="{{ $documentGroup->id }}" />
					@endcomponent
				</div>
			@endslot
		@endforeach
	@endcomponent
@endsection
