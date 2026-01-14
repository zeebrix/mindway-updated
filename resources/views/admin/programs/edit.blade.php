@extends('layouts.app')
@section('selected_menu', 'active')

@section('content')
<h1 class="fw-bolder">
    @if ($user?->programDetail?->program_type == 0)
    Deactivated :
    @else
    Manage
    @endif
    {{ $user?->programDetail?->company_name }}
    @if ($user?->programDetail?->program_type == 2)
    Trial
    @endif Program
</h1>
<a href="{{ route('admin.programs.get-analytics-data',$user->id) }}" class="btn btn-primary mindway-btn-blue mt-2 mb-2">
    View Analytics <i class="bi bi-bar-chart-fill ms-1"></i>
</a>

<div class="program-form">
    @include('layouts.partials.alerts')

    <form id="updateProgramForm" action="{{ route('admin.programs.update',$user->id)}}" method="POST" enctype="multipart/form-data">

        @csrf
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" id="user_id" name="user_id" value="{{$user->id}}">

        <div class="row">
            @include('admin.programs.input-component', [
            'id' => 'company_name',
            'name' => 'company_name',
            'label' => 'Company Name',
            'type' => 'text',
            'placeholder' => 'Enter Company Name',
            'is_required' => true,
            'value' => $user?->programDetail?->company_name,
            ])

            <div class="d-flex gap-3 flex-wrap">

                <div class="card card-small">
                    <div class="card-body">
                        <label for="codeId" class="form-label">Access Code</label>
                        <input type="text" class="form-control custom-input-field" id="codeId" name="code" placeholder="ACCESSCODE" value="{{ $user?->programDetail?->code }}">
                    </div>
                </div>

                <div class="card flex-grow-1">
                    <div class="card-body">
                        <label class="form-label">Departments</label>
                        <div id="departmentList" class="department-list"></div>
                        <button type="button" class="btn btn-link add-department-btn" data-bs-toggle="modal" data-bs-target="#add-department">Add Department</button>
                    </div>
                </div>
            </div>

            @include('admin.programs.input-component', [
            'id' => 'max_lic',
            'name' => 'max_lic',
            'label' => 'Licenses',
            'type' => 'number',
            'placeholder' => '5',
            'is_required' => true,
            'value' => $user?->programDetail?->max_lic,
            ])
            @include('admin.programs.upload-component', [
            'id' => 'logo',
            'name' => 'logo',
            'label' => 'Upload Logo',
            'existing_image' => $user->programDetail->logo ?? null,
            'required' => true
            ])



            <!-- <div class="col-12">
                <label class="form-label">Employees Visible?</label>
                <div class="btn-group-toggle d-flex gap-2 flex-wrap">
                    <input type="radio" class="btn-check" name="allow_employees" id="yes-employee" value="yes" autocomplete="off" {{ $user?->programDetail?->allow_employees == '1' ? 'checked' : '' }} required>
                    <label class="btn btn-outline-primary rounded-pill" for="yes-employee">Yes</label>

                    <input type="radio" class="btn-check" name="allow_employees" id="no-employee" value="no" autocomplete="off" {{ $user?->programDetail?->allow_employees == '0' ? 'checked' : '' }} required>
                    <label class="btn btn-outline-primary rounded-pill" for="no-employee">No</label>
                </div>
            </div> -->
            @include('admin.programs.input-component', [
            'id' => 'link',
            'name' => 'link',
            'label' => 'Booking Link',
            'type' => 'text',
            'placeholder' => 'Enter Booking Link',
            'is_required' => true,
            'value' => $user?->programDetail?->link,
            ])

            @include('admin.programs.input-component', [
            'id' => 'max_session',
            'name' => 'max_session',
            'label' => 'Max Session',
            'type' => 'number',
            'placeholder' => 'Enter Max Session',
            'is_required' => true,
            'value' => $user?->programDetail?->max_lic,
            ])

            @if($user->program_type == 2)
            @include('admin.programs.input-component', [
            'id' => 'trial_expire',
            'name' => 'trial_expire',
            'label' => 'Trial Expire Date',
            'type' => 'date',
            'value' => \Carbon\Carbon::parse($user?->programDetail?->trial_expire ?? '')->format('Y-m-d'),
            ])
            @endif

            @if($user?->programDetail?->program_type == 1)
            <h4>Payment / Pricing</h4>
            <label class="form-label">Plan Type</label>
            <div class="btn-group-toggle" data-toggle="buttons">
                @foreach(['Pay As You Go', 'Standard', 'Premium'] as $plan)
                <label class="btn btn-outline-primary rounded-pill">
                    <input type="radio" class="btn-check" name="plan_type" value="{{ $plan }}" @if($user?->ProgramPlan?->plan_type == $plan) checked @endif> {{ $plan }}
                </label>
                @endforeach
            </div>

            @include('admin.programs.input-component', [
            'id' => 'annual_fee',
            'name' => 'annual_fee',
            'label' => 'Annual Fee',
            'type' => 'number',
            'placeholder' => 'Annual Fee ',
            'value' => $user?->ProgramPlan?->annual_fee,
            ])

            @include('admin.programs.input-component', [
            'id' => 'cost_per_session',
            'placeholder' => 'Cost Per session ',
            'name' => 'cost_per_session',
            'label' => 'Cost per session',
            'type' => 'number',
            'value' => $user?->programPlan?->cost_per_session,
            ])

            @include('admin.programs.input-component', [
            'id' => 'renewal_date',
            'name' => 'renewal_date',
            'label' => 'Renewal Date',
            'placeholder' => 'Renewal Date ',
            'type' => 'text',
            'value' => \Carbon\Carbon::parse($user->programPlan?->renewal_date)->format('d/m'),
            ])

            <label class="form-label">GST? +10%</label>
            <div class="btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-outline-primary rounded-pill">
                    <input type="radio" class="btn-check" name="gst_registered" value="1" @if($user->programPlan?->gst_registered) checked @endif> Yes
                </label>
                <label class="btn btn-outline-primary rounded-pill">
                    <input type="radio" class="btn-check" name="gst_registered" value="0" @if(!$user->programPlan?->gst_registered) checked @endif> No
                </label>
            </div>
            @endif
        </div>

        <div class="form-actions text-center">
            @if ($user->program_type == 1)
            <div class="d-flex justify-content-center">
                <a class="btn btn-primary mb-2 mindway-btn-blue me-2"
                    href="{{ url('/manage-admin/deactive-program/' . $user->id . '/deactivate') }}">
                    Deactivate
                </a>
                <button id="submitButton" type="submit" class="btn btn-primary mb-2 mindway-btn-blue">Update
                    Program</button>
            </div>
            @endif

            @if ($user->program_type == 0)
            <div class="d-flex justify-content-center flex-wrap">
                <button type="button"
                    class="btn btn-primary mindway-btn-blue mx-2 mb-2 js-program-action"
                    data-action="active"
                    data-user-id="{{ $user->id }}">
                    Make Active Program
                </button>
                <button type="button"
                    class="btn btn-primary mindway-btn-blue mx-2 mb-2 js-program-action"
                    data-action="extend_trial"
                    data-user-id="{{ $user->id }}">
                    Extend Trial By 14 days
                </button>
                @if(loginUser() !== 'csm')
                <button type="button"
                    class="btn btn-primary mindway-btn-blue mx-2 mb-2 js-program-action"
                    data-action="deactivate"
                    data-user-id="{{ $user->id }}">
                    Delete Permanently
                </button>
                @endif
                <button id="submitButton" type="submit" class="btn btn-primary mx-2 mb-2 mindway-btn-blue">
                    Update Program
                </button>

            </div>
            @endif
        </div>
    </form>
    <form id="programActionForm"
        action="{{ route('admin.programs.status', $user->id) }}"
        method="POST"
        class="d-none">
        @csrf
        <input type="hidden" name="action" id="action" value="">
    </form>
    <div class="d-flex justify-content-start align-items-center mb-4">
        <b>
            <a href="#" class="add-link me-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Add Individual
            </a>
        </b>

        <b>
            <a href="#" class="add-link" data-bs-toggle="modal" data-bs-target="#addSessionModalBulk">
                Add Bulk
            </a>
        </b>
    </div>

    <div class="card w-100">
        <div class="card-body p-4">

            <div class="employee-header d-flex justify-content-start align-items-baseline">
                <h3 class="fw-normal">Employees ({{($totalCustomers) }})</h3>

                <div class="search-input"></div>

                <h2 class="level-title fw-bold">Level</h2>
                <h2 class="session-left-title fw-bold">Session Left</h2>
            </div>

            <div class="table-responsive">
                <table class="table text-nowrap mb-0 align-middle" width="100%" id="Yajra-dataTable"></table>
            </div>

        </div>
    </div>

</div>
<input type="hidden" id="departmentsData" value='@json($user->programDepartment)'>


@include("admin.programs.modal.add-department")
@include("program.employees.add")
@include("program.employees.add-bulk")

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/dashboard/css/program-form.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/dashboard/js/program-form.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
@endpush