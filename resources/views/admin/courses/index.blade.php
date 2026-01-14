@extends('layouts.app')

@section('content')
@include('components.datatable', [
    'tableId' => 'courses-datatable',
    'title' => 'courses Management',
    'subtitle' => '',
    'dataTableConfig' => $dataTableConfig,
    'breadcrumbs' => [
        ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['name' => 'courses', 'url' => null],
    ]
])
@endsection