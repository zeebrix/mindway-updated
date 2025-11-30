@include('components.datatable', [
    'tableId' => 'csms-datatable',
    'title' => 'Customer Success Manager',
    'subtitle' => '',
    'dataTableConfig' => $dataTableConfig,
    'breadcrumbs' => [
        ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['name' => 'Customer Success manager', 'url' => null],
    ]
])