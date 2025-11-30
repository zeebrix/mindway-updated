@include('components.datatable', [
    'tableId' => 'users-datatable',
    'title' => 'Users Management',
    'subtitle' => '',
    'dataTableConfig' => $dataTableConfig,
    'breadcrumbs' => [
        ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['name' => 'Users', 'url' => null],
    ]
])