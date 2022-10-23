@extends('layouts.dashboard', ['pageTitle' => 'Open Approvals'])

@section('page')
	@include('components.form-messages')
	@include('components.open-approvals-sampler')
@endsection
