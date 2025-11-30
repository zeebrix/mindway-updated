@extends('layouts.app')

@section('selected_menu', 'active')
@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('assets/dashboard/program/css/employees-page.css') }}">
@endpush
@section('content')


<div class="row">
    <div class="col-10 offset-1">

        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <img style="object-fit: contain;" height="46px" width="130px" class="popup"
                    src="{{ asset('storage/logo/' . $user->ProgramDetail->logo) }}" alt="{{ $user->ProgramDetail->company_name }} Logo">
            </div>
            <div class="trial-info">
                @if ($is_trial)
                <p><b>On Free Trial:</b> <span>{{ $leftDays }} days left of trial</span></p>
                @endif
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <div class="d-flex align-items-center">
                    <h4 class="mb-0"><strong>Manage Employees</strong></h4>
                    <a href="#" class="mindway-btn btn btn-primary ms-2 nowrap" data-bs-toggle="modal"
                        data-bs-target="#exampleModal">Add Individual</a>

                </div>
                <h4>Add and remove employees in your EAP program</h4>
            </div>
        </div>

        @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif
        @if ($errors->any())
        @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
        @endforeach
        @endif

        <div class="d-flex align-items-center employee-list-header">
            <h3 class="fw-normal mb-0 me-4"><b>Employees</b> ({{ count($customers) }})</h3>

            <div class="search-input">
                <i class="ti ti-search search-icon-custom"></i>
                <input type="text" id="searchInput" class="form-control search-input-field" placeholder="Search details">
            </div>

            <h2 class="fw-bold level-header ms-auto">Level</h2>
        </div>

        <div class="table-responsive">
            <table class="table text-nowrap mb-0 align-middle" id="employeeTable">
                <tbody>
                    @foreach ($customers as $data)
                    @if($data->level == 'admin')
                    <tr>
                        <td class="border-bottom-0" style="width: 350px;"><span
                                class=" fw-semibold">{{ $data->name }}</span><br>
                            <span class=" fw-normal">{{ $data->email }}</span>
                        </td>
                        <td class="border-bottom-0" id="changeLevel" style="width: 250px;" data-id="{{ $data->id }}" data-level="{{ $data->level }}"><span
                                class="{{ $data->level == 'member' ? 'member-style' : 'admin-style'}} badge btn btn-primary theme-btn">{{$data->level??'member'}}</span>
                        </td>
                        <td class="border-bottom-0">
                            <button type="button"
                                class="mindway-btn btn btn-success btn-sm remove-btn remove-btn-custom"
                                data-name="{{ $data->name }}" data-email="{{ $data->email }}"
                                data-id="{{ $data->id }}">
                                Remove
                            </button>
                        </td>
                    </tr>

                    @else
                    <tr>
                        <td class="border-bottom-0" style="width: 350px;"><span
                                class=" fw-semibold">{{ $data->name }}</span><br>
                            <span class=" fw-normal">{{ $data->email }}</span>
                        </td>
                        <td class="border-bottom-0" id="changeLevel" style="width: 250px;" data-id="{{ $data->id }}" data-level="{{ $data->level }}"><span
                                class="{{ $data->level == 'member' ? 'member-style' : 'admin-style'}} badge btn btn-primary theme-btn">{{$data->level??'member'}}</span>
                        </td>
                        <td class="border-bottom-0">
                            <button type="button"
                                class="mindway-btn btn btn-success btn-sm remove-btn remove-btn-custom"
                                data-name="{{ $data->name }}" data-email="{{ $data->email }}"
                                data-id="{{ $data->id }}">
                                Remove
                            </button>
                        </td>
                    </tr>

                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


@include('program.employees.add')

<div class="modal" id="adminLevelModal" tabindex="-1" role="dialog" aria-labelledby="adminLevelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content admin-level-modal" style="width:80%">
            <div class="modal-body">
                <input type="hidden" value="" name="memberIdInput" id="memberIdInput">
                <div class="d-flex flex-wrap justify-content-start admin-level-modal-body">
                    <!-- Member -->
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="level" id="levelMember" value="member" autocomplete="off">
                        <label class="mindway-btn btn btn-sm btn-outline-primary rounded-pill px-4 plan-type-checkbox level-btn-member"
                            for="levelMember">Member</label>
                    </div>
                    <!-- Admin -->
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="level" id="levelAdmin" value="admin" autocomplete="off">
                        <label class="mindway-btn btn-sm plan-type-checkbox btn btn-outline-primary rounded-pill px-4 level-btn-admin"
                            for="levelAdmin">Admin</label>
                    </div>

                </div>
                <button type="submit" id="submitAdminLevel"
                    class="btn btn-sm btn-primary mindway-btn-blue confirm-level-btn">Confirm</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- External dependencies remain here --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.body.dataset.removeCustomerRoute = "{{ route('program.employee.remove') }}";
</script>

{{-- Link to external JavaScript file for custom logic --}}
<script src="{{ asset('assets/dashboard/program/js/employees-page.js') }}"></script>
@endpush