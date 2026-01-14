@extends('layouts.app')

@section('content')
@include('components.datatable', [
    'tableId' => 'lessons-datatable',
    'title' => 'Lessons Management',
    'subtitle' => '',
    'dataTableConfig' => $dataTableConfig,
    'breadcrumbs' => [
        ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['name' => 'lessons', 'url' => null],
    ]
])
@endsection