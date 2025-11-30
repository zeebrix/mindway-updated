@extends('layouts.app')

@section('content')
    @include('components.datatable', [
        'tableId' => $tableId,
        'title' => $title,
        'dataTableConfig' => $dataTableConfig,
    ])
@endsection
