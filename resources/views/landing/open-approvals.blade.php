@extends('layouts.landing')

@section('page')
	@include('components.open-approvals-sampler', [
		'apply' => false
	])
@endsection
