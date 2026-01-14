@extends('layouts.app')

@section('selected_menu', 'active')


@include('components.datatable', [
'tableId' => $tableId,
'title' => '',
'dataTableConfig' => $dataTableConfig,
'breadcrumbs' => [
['name' => 'Dashboard', 'url' => route('admin.counselors.index')],
['name' => 'All Counsellors', 'url' => null],
]
])

@section('modal')
<x-counsellor-modal
    :id="'addCounsellorModal'"
    :title="'Add New Counsellor'"
    :action="route('admin.counselors.store')"
    :locations="$locations ?? []"
    :languages="$languages ?? []"
    :timezones="$timezones['timezones'] ?? []"
    :specializations="$specialization ?? []" />
@endsection