
@section('content')
@if(isset($breadcrumbs) && is_array($breadcrumbs))
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        @foreach($breadcrumbs as $breadcrumb)
        @if($loop->last)
        <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['name'] }}</li>
        @else
        <li class="breadcrumb-item">
            <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a>
        </li>
        @endif
        @endforeach
    </ol>
</nav>
@endif
@if(($isProgram??false))
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="fw-bolder">Programs</h1>
    <div>
        @if(($status ?? 0) == 1)
        <a href="{{ route('admin.programs.create', ['type' => 1]) }}" class="btn btn-primary mindway-btn-blue">Create Active Program</a>
        @elseif(($status ?? 0) == "2")
        <a href="{{ route('admin.programs.create', ['type' => 2]) }}" class="btn btn-primary mindway-btn-blue">Create Trial Program</a>
        @endif
    </div>
</div>
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ $status == '1' ? 'active' : '' }}" href="{{ route('admin.programs.index', ['status' => 1]) }}">Active</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $status == '2' ? 'active' : '' }}" href="{{ route('admin.programs.index', ['status' => 2]) }}">Trial</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $status == '0' ? 'active' : '' }}" href="{{ route('admin.programs.index', ['status' => 0]) }}">Deactivated</a>
    </li>
</ul>
@endif
<div class="card w-100">
    <div class="card-body p-4">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="card-title fw-semibold mb-1">{{ $title ?? 'Data Table' }}</h5>
                @if(isset($subtitle))
                <p class="text-muted mb-0">{{ $subtitle }}</p>
                @endif
            </div>

            <div class="d-flex gap-2">
                @if(isset($exportButtons) && is_array($exportButtons))
                @foreach($exportButtons as $button)
                <button type="button"
                    class="{{ $button['class'] ?? 'btn btn-outline-secondary btn-sm' }}"
                    data-export="{{ $button['type'] ?? 'csv' }}"
                    data-table="{{ $tableId ?? 'data-table' }}">
                    @if(isset($button['icon']))
                    <i class="{{ $button['icon'] }}"></i>
                    @endif
                    {{ $button['text'] ?? 'Export' }}
                </button>
                @endforeach
                @endif
                @if(isset($addNewUrl))
                <a href="{{ $addNewUrl }}" id="{{ $addNewBtnId }}" data-bs-toggle="modal" data-bs-target="#addCounsellorModal" class="btn mindway-btn-blue">
                    @if(isset($addNewIcon))
                    <i class="{{ $addNewIcon }}"></i>
                    @endif
                    {{ $addNewText ?? 'Add New' }}
                </a>
                @endif
            </div>
        </div>

        @if(isset($filters) && is_array($filters))
        <div class="row mb-4">
            @foreach($filters as $filter)
            <div class="col-md-{{ $filter['col_size'] ?? '3' }}">
                <div class="mb-3">
                    <label for="{{ $filter['id'] }}" class="form-label">{{ $filter['label'] }}</label>
                    @if($filter['type'] === 'select')
                    <select class="form-select" id="{{ $filter['id'] }}" data-filter="{{ $filter['column'] }}">
                        <option value="">{{ $filter['placeholder'] ?? 'All' }}</option>
                        @if(isset($filter['options']))
                        @foreach($filter['options'] as $value => $text)
                        <option value="{{ $value }}">{{ $text }}</option>
                        @endforeach
                        @endif
                    </select>
                    @elseif($filter['type'] === 'date')
                    <input type="date" class="form-control" id="{{ $filter['id'] }}" data-filter="{{ $filter['column'] }}">
                    @elseif($filter['type'] === 'daterange')
                    <div class="input-group">
                        <input type="date" class="form-control" id="{{ $filter['id'] }}_from" data-filter="{{ $filter['column'] }}_from" placeholder="From">
                        <span class="input-group-text">to</span>
                        <input type="date" class="form-control" id="{{ $filter['id'] }}_to" data-filter="{{ $filter['column'] }}_to" placeholder="To">
                    </div>
                    @else
                    <input type="{{ $filter['type'] ?? 'text' }}"
                        class="form-control"
                        id="{{ $filter['id'] }}"
                        data-filter="{{ $filter['column'] }}"
                        placeholder="{{ $filter['placeholder'] ?? '' }}">
                    @endif
                </div>
            </div>
            @endforeach

            <div class="col-md-12">
                <button type="button" class="btn btn-primary btn-sm" id="apply-filters">Apply Filters</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-filters">Clear Filters</button>
            </div>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table text-nowrap mb-0 align-middle"
                id="{{ $tableId ?? 'data-table' }}"
                data-config="{{ json_encode($dataTableConfig ?? []) }}">
                <thead class="text-dark fs-4">
                    <tr>
                        @if(isset($dataTableConfig['columns']))
                        @foreach($dataTableConfig['columns'] as $column)
                        <th class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">{{ $column['title'] ?? ucfirst($column['name']) }}</h6>
                        </th>
                        @endforeach
                        @endif
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <div id="{{ $tableId ?? 'data-table' }}-loading" class="datatable-loading display-none">
            <div class="d-flex justify-content-center align-items-center h-100">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

@if(isset($additionalContent))
<div class="mt-4">
    {!! $additionalContent !!}
</div>
@endif
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="{{ asset('assets/dashboard/css/datatable.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="{{ asset('assets/dashboard/js/dynamic-datatable.js') }}"></script>
@endpush