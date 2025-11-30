@include('components.datatable', [
    'tableId' => 'courses-audio-datatable',
    'title' => 'Course Audio',
    'subtitle' => '',
    'dataTableConfig' => $dataTableConfig,
    'breadcrumbs' => [
        ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['name' => 'courses', 'url' => null],
    ]
])