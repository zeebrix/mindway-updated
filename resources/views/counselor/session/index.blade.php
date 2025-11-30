@extends('layouts.app')

@section('selected_menu', 'active')

@section('content')

<div class="row">
    <div class="col-10 offset-1">

        <div class="mb-4">
            <h2><b>Add Sessions to Employees</b></h2>

            <div class="search-input">
                <i class="ti ti-search"></i>
                <input type="text" id="searchInput" class="form-control" placeholder="Search by Id, name, email, or company">
            </div>
        </div>

        {{-- Alerts --}}
        @if(session()->has('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if(session()->has('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        @if($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger">{{ $error }}</div>
            @endforeach
        @endif


        {{-- DataTable --}}
        <div class="table-responsive">
            <table class="table text-nowrap mb-0 align-middle" id="Yajra-dataTable">
                <thead class="text-dark fs-4">
                    <tr>
                        <th class="col-number"><h6 class="fw-semibold mb-0">#</h6></th>
                        <th class="col-employee"><h6 class="fw-semibold mb-0">Employee Details</h6></th>
                        <th class="col-company"><h6 class="fw-semibold mb-0">Company</h6></th>
                        <th class="col-remaining"><h6 class="fw-semibold mb-0">Remaining</h6></th>
                        <th class="col-actions"><h6 class="fw-semibold mb-0"></h6></th>
                    </tr>
                </thead>
            </table>
        </div>
        @include('counselor.session.modal.add')
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/add-sessions.css') }}">
@endpush

@push('scripts')
@include('counselor.session.datatable')
@include('counselor.session.modal.add')
@include('counselor.session.list')

<script src="{{ asset('assets/js/add-sessions.js') }}"></script>
@endpush
