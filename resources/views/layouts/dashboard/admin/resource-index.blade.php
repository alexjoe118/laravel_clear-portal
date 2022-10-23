@php
    $create = $create ?? true;
@endphp

@extends('layouts.dashboard', [
    'pageTitle' => Str::of($resource)->headline()->plural(),
])

@if ($create)
    @section('button')
        @include('components.button', [
            'title' => 'Create ' . Str::headline($resource),
            'url' => route('admin.' . $resource . '.create'),
        ])
    @endsection

@endif

@if ($resource == 'user' || $resource == 'loan-request' || $resource == 'loan' || $resource == 'open-approvals' || $resource == 'documents')
    @section('button')
        @include('components.form-fields', [
            'fields' => [
                [
                    'width' => 'first',
                    'type' => 'select',
                    'options' => $selectOptions['pageCount'],
                    'defaultValue' => $countperpage,
                    'input' => [
                        'onchange' => 'onChangeFilter(\''.$resource.'\')',
                        'onlyitem' => true,
                        'id' => 'changepage'
                    ],
                ],
            ],
        ])
        @if($resource == 'user')
            @include('components.auto-complete-select', [
                'fields' => [
                    [
                        'data_user' => $all_users,
                        'search_word'=> $searchword,
                        'width' => 'third',
                    ],
                ],
            ])
        @endif
    @endsection
@endif

<script>
    function onChangeFilter(resource) {
        const obj = document.getElementById("changepage");
        window.location.href='/dashboard/admin/'+resource+'?pagecount='+obj.value;
    }
</script>


@section('page')
    @include('components.form-messages')

    @yield('listing')
@endsection
