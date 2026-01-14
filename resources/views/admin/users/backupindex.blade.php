@extends('layouts.app')

@section('content')
@include('components.datatable', [
    'tableId' => 'users-datatable',
    'title' => 'Users Management',
    'subtitle' => 'Manage all system users and their permissions',
    'addNewUrl' => route('admin.users.create'),
    'addNewText' => 'Add New User',
    'addNewIcon' => 'fas fa-plus',
    'dataTableConfig' => $dataTableConfig,
    'breadcrumbs' => [
        ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['name' => 'Users', 'url' => null],
    ],
    'filters' => [
        [
            'id' => 'role-filter',
            'label' => 'Role',
            'type' => 'select',
            'column' => 'role',
            'col_size' => '3',
            'placeholder' => 'All Roles',
            'options' => [
                'admin' => 'Administrator',
                'user' => 'User',
                'csm' => 'Customer Success Manager',
                'counselor' => 'Counselor',
            ],
        ],
        [
            'id' => 'status-filter',
            'label' => 'Status',
            'type' => 'select',
            'column' => 'status',
            'col_size' => '3',
            'placeholder' => 'All Statuses',
            'options' => [
                'active' => 'Active',
                'inactive' => 'Inactive',
                'suspended' => 'Suspended',
            ],
        ],
        [
            'id' => 'created-date-filter',
            'label' => 'Registration Date',
            'type' => 'daterange',
            'column' => 'created_at',
            'col_size' => '4',
        ],
        [
            'id' => 'search-filter',
            'label' => 'Quick Search',
            'type' => 'text',
            'column' => 'global',
            'col_size' => '2',
            'placeholder' => 'Search users...',
        ],
    ],
    'exportButtons' => [
        [
            'type' => 'csv',
            'text' => 'Export CSV',
            'class' => 'btn btn-outline-success btn-sm',
            'icon' => 'fas fa-file-csv',
        ],
        [
            'type' => 'excel',
            'text' => 'Export Excel',
            'class' => 'btn btn-outline-primary btn-sm',
            'icon' => 'fas fa-file-excel',
        ],
        [
            'type' => 'pdf',
            'text' => 'Export PDF',
            'class' => 'btn btn-outline-danger btn-sm',
            'icon' => 'fas fa-file-pdf',
        ],
    ],
])
@endsection