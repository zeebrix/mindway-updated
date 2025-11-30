@extends('layouts.app')
@section('selected_menu', 'active')

@section('content')
<h1 class="fw-bolder">
    @if ($program?->program_type == 0)
    Deactivated :
    @else
    Manage
    @endif
    {{ $program->company_name }}
    @if ($program?->program_type == 2)
    Trial
    @endif Program
</h1>

<a href="{{ url('/manage-admin/view-analytics') }}?program_id={{ $program->id }}" class="btn btn-primary mindway-btn-blue mt-2 mb-2">
    View Analytics <i class="bi bi-bar-chart-fill ms-1"></i>
</a>

<div class="program-form">
    @if(session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    @if($errors->any())
    @foreach($errors->all() as $error)
    <div class="alert alert-danger">{{ $error }}</div>
    @endforeach
    @endif

    <form id="updateProgramForm" action="{{ url('/manage-admin/update-program', ['id' => $program->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            {{-- Company Name --}}
            @include('admin.programs.input-component', [
            'id' => 'company_name',
            'name' => 'company_name',
            'label' => 'Company Name',
            'type' => 'text',
            'placeholder' => 'Enter Company Name',
            'is_required' => true,
            'value' => $program->company_name,
            ])

            {{-- Access Code --}}
            @include('admin.programs.input-component', [
            'id' => 'access_code',
            'name' => 'code',
            'label' => 'Access Code',
            'type' => 'text',
            'placeholder' => 'ACCESSCODE',
            'value' => $program->code,
            ])

            {{-- Departments --}}
            <div class="department-section">
                <label class="form-label">Departments</label>
                <div id="departmentList" class="department-list"></div>
                <button type="button" class="btn btn-link add-department-btn" data-bs-toggle="modal" data-bs-target="#add-department">Add Department</button>
                <input type="hidden" name="departments" id="departments">
            </div>

            {{-- Licenses --}}
            @include('admin.programs.input-component', [
            'id' => 'max_lic',
            'name' => 'max_lic',
            'label' => 'Licenses',
            'type' => 'number',
            'placeholder' => '5',
            'is_required' => true,
            'value' => $program->max_lic,
            ])

            {{-- Upload Logo --}}
            @include('admin.programs.upload-component', [
            'id' => 'logo',
            'name' => 'logo',
            'label' => 'Upload Logo',
            'existing_image' => $program->logo,
            ])

            {{-- Employees Visible --}}
            <label class="form-label">Employees Visible?</label>
            <div class="btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-outline-primary">
                    <input type="radio" name="allow_employees" value="1" @if($program?->allow_employees == '1') checked @endif> Yes
                </label>
                <label class="btn btn-outline-primary">
                    <input type="radio" name="allow_employees" value="0" @if($program?->allow_employees == '0') checked @endif> No
                </label>
            </div>

            {{-- Booking Link --}}
            @include('admin.programs.input-component', [
            'id' => 'link',
            'name' => 'link',
            'label' => 'Booking Link',
            'type' => 'text',
            'placeholder' => 'Enter Booking Link',
            'is_required' => true,
            'value' => $program->link,
            ])

            {{-- Max Session --}}
            @include('admin.programs.input-component', [
            'id' => 'max_session',
            'name' => 'max_session',
            'label' => 'Max Session',
            'type' => 'number',
            'placeholder' => 'Enter Max Session',
            'is_required' => true,
            'value' => $program->max_session,
            ])

            {{-- Trial Expire --}}
            @if($program->program_type == 2)
            @include('admin.programs.input-component', [
            'id' => 'trial_expire',
            'name' => 'trial_expire',
            'label' => 'Trial Expire Date',
            'type' => 'date',
            'value' => \Carbon\Carbon::parse($program->trial_expire ?? '')->format('Y-m-d'),
            ])
            @endif

            {{-- Active Program Pricing --}}
            @if($program->program_type == 1)
            <h4>Payment / Pricing</h4>

            <label class="form-label">Plan Type</label>
            <div class="btn-group-toggle" data-toggle="buttons">
                @foreach(['Pay As You Go', 'Standard', 'Premium'] as $plan)
                <label class="btn btn-outline-primary">
                    <input type="radio" name="plan_type" value="{{ $plan }}" @if($ProgramPlan?->plan_type == $plan) checked @endif> {{ $plan }}
                </label>
                @endforeach
            </div>

            @include('admin.programs.input-component', [
            'id' => 'annual_fee',
            'name' => 'annual_fee',
            'label' => 'Annual Fee',
            'type' => 'number',
            'value' => $ProgramPlan?->annual_fee,
            ])

            @include('admin.programs.input-component', [
            'id' => 'cost_per_session',

            'name' => 'cost_per_session',
            'label' => 'Cost per session',
            'type' => 'number',
            'value' => $ProgramPlan?->cost_per_session,
            ])

            @include('admin.programs.input-component', [
            'id' => 'renewal_date',
            'name' => 'renewal_date',
            'label' => 'Renewal Date',
            'type' => 'text',
            'value' => \Carbon\Carbon::parse($ProgramPlan?->renewal_date)->format('d/m'),
            ])

            <label class="form-label">GST? +10%</label>
            <div class="btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-outline-primary">
                    <input type="radio" name="gst_registered" value="1" @if($ProgramPlan?->gst_registered) checked @endif> Yes
                </label>
                <label class="btn btn-outline-primary">
                    <input type="radio" name="gst_registered" value="0" @if(!$ProgramPlan?->gst_registered) checked @endif> No
                </label>
            </div>
            @endif
        </div>

        <div class="form-actions text-center">
            @if ($program->program_type == 1)
            <div class="d-flex justify-content-center">
                <a class="btn btn-primary mb-2 mindway-btn-blue me-2"
                    href="{{ url('/manage-admin/deactive-program/' . $program->id . '/deactivate') }}">
                    Deactivate
                </a>
                <button id="submitButton" type="submit" class="btn btn-primary mb-2 mindway-btn-blue">Update
                    Program</button>
            </div>
            @endif

            @if ($program->program_type == 0)
            <div class="d-flex justify-content-center flex-wrap">
                <a class="btn btn-primary mindway-btn-blue mx-2 mb-2"
                    href="{{ url('/manage-admin/deactive-program/' . $program->id . '/active') }}">
                    Make Active Program
                </a>
                <a class="btn btn-primary mindway-btn-blue mx-2 mb-2"
                    href="{{ url('/manage-admin/deactive-program/' . $program->id . '/extend_trial') }}">
                    Extend Trial By 14 days
                </a>
                @if(loginUser() !== 'csm')
                <a class="btn btn-primary mindway-btn-blue mx-2 mb-2"
                    href="{{ url('/manage-admin/deactive-program/' . $program->id . '/delete') }}">
                    Delete Permanently
                </a>
                @endif
                <button id="submitButton" type="submit" class="btn btn-primary mx-2 mb-2 mindway-btn-blue">
                    Update Program
                </button>

            </div>
            @endif
        </div>
    </form>

    {{-- Employees Table --}}
    <div class="card mt-4">
        <div class="card-body">
            <h4>Employees ({{ count($customers) }})</h4>
            <table class="table table-striped table-bordered" id="programEmployeesTable"></table>
        </div>
    </div>
</div>



@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/dashboard/css/program-form.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/dashboard/js/program-form.js') }}"></script>
@endpush