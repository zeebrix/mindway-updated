@extends('layouts.app')

@section('content')
   
    @include('components.datatable', [
        'tableId' => $tableId,
        'title' => '', 
        'dataTableConfig' => $dataTableConfig,
    ])
@endsection
