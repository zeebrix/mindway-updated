@extends('layouts.app')

@section('selected_menu', 'active')

@section('content')
<h1 class="fw-bolder mb-2">{{ $type == 1 ? 'Create New Active Program' : 'Create Trial Program' }}</h1>

<h4 class="fw-bolder mb-2">Company Details</h4>

@include('layouts.partials.alerts')

<form action="{{ route('admin.programs.store') }}" method="POST" enctype="multipart/form-data" class="program-form">
    @csrf
    <input type="hidden" name="program_type" value="{{ $type }}">

    <div class="row g-3">

        @include('admin.programs.input-component', [
        'name' => 'company_name',
        'label' => 'Company Name',
        'type' => 'text',
        'placeholder' => 'Enter Company Name',
        'is_required' => true,
        'id' => 'company_nameId',
        'value' => old('company_name')
        ])

        <div class="d-flex gap-3 flex-wrap">

            <div class="card card-small">
                <div class="card-body">
                    <label for="codeId" class="form-label">Access Code</label>
                    <input type="text" class="form-control custom-input-field" id="codeId" name="code" placeholder="ACCESSCODE" value="{{ old('code') }}">
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

        <input type="hidden" name="departments" id="departments">

        @include('admin.programs.input-component', [
        'name' => 'max_lic',
        'label' => 'Licenses',
        'type' => 'number',
        'placeholder' => '1000',
        'is_required' => true,
        'id' => 'max_licId',
        'value' => old('max_lic')
        ])

        @include('admin.programs.input-component', [
        'name' => 'max_session',
        'label' => 'Max Sessions',
        'type' => 'text',
        'placeholder' => '5',
        'is_required' => true,
        'id' => 'max_sessionId',
        'value' => old('max_session')
        ])

        @include('admin.programs.input-component', [
        'name' => 'link',
        'label' => 'Booking Link',
        'type' => 'url',
        'placeholder' => 'https://example.com/booking',
        'is_required' => true,
        'id' => 'linkId',
        'value' => old('link')
        ])

        <div class="col-12 me-3">
    <div class="card upload-logo-card">
        <div id="uploadLogoTrigger" class="upload-logo-trigger">
            <div class="d-flex">
                <label for="logoId" class="form-label mb-1 upload-logo-label">
                    <span>Upload Logo</span>
                </label>
                <input type="file" class="form-control upload-logo-input" id="logoId" name="logo" required>
                <div>
                    <img id="previewImage" class="upload-logo-preview" src="{{ asset('/images/upload.png') }}" alt="logo image">
                </div>
            </div>
        </div>
    </div>
</div>



        {{-- Employees Visible --}}
        <div class="col-12">
            <label class="form-label">Employees Visible?</label>
            <div class="btn-group-toggle d-flex gap-2 flex-wrap">
                <input type="radio" class="btn-check" name="allow_employees" id="yes-employee" value="yes" autocomplete="off" {{ old('allow_employees') == 'yes' ? 'checked' : '' }} required>
                <label class="btn btn-outline-primary rounded-pill" for="yes-employee">Yes</label>

                <input type="radio" class="btn-check" name="allow_employees" id="no-employee" value="no" autocomplete="off" {{ old('allow_employees') == 'no' ? 'checked' : '' }} required>
                <label class="btn btn-outline-primary rounded-pill" for="no-employee">No</label>
            </div>
        </div>

        <span class="fw-bolder mb-3 d-block">Assign Admin User</span>

        @include('admin.programs.input-component', [
        'name' => 'full_name',
        'label' => 'Full Name',
        'type' => 'text',
        'placeholder' => 'Ryder Mckenzie',
        'is_required' => true,
        'id' => 'full_nameId',
        'value' => old('full_name')
        ])

        @include('admin.programs.input-component', [
        'name' => 'admin_email',
        'label' => 'Email',
        'type' => 'email',
        'placeholder' => 'your@email.com',
        'is_required' => true,
        'id' => 'admin_emailId',
        'value' => old('admin_email')
        ])

        @if ($type == 2)
        @include('admin.programs.input-component', [
        'name' => 'trial_expire',
        'label' => 'Trial Expire Date',
        'type' => 'date',
        'placeholder' => 'Enter Expire Date',
        'is_required' => true,
        'id' => 'trial_expireId',
        'value' => old('trial_expire')
        ])
        @endif

        {{-- Payment/Pricing for Active Programs --}}
        <div id="active-program" class="{{ $type == 1 ? 'd-block' : 'd-none' }}">
            <span class="fw-bolder my-3 d-block">Payment/Pricing</span>
            <label class="form-label">Plan Type</label>
            <div class="btn-group-toggle d-flex gap-2 flex-wrap">
                <input type="radio" class="btn-check" name="plan_type" id="payg" value="Pay As You Go" autocomplete="off" {{ old('plan_type') == 'Pay As You Go' ? 'checked' : '' }}>
                <label class="btn btn-outline-primary rounded-pill" for="payg">Pay As You Go</label>

                <input type="radio" class="btn-check" name="plan_type" id="standard" value="Standard" autocomplete="off" {{ old('plan_type') == 'Standard' ? 'checked' : '' }}>
                <label class="btn btn-outline-primary rounded-pill" for="standard">Standard</label>

                <input type="radio" class="btn-check" name="plan_type" id="premium" value="Premium" autocomplete="off" {{ old('plan_type') == 'Premium' ? 'checked' : '' }}>
                <label class="btn btn-outline-primary rounded-pill" for="premium">Premium</label>
            </div>

            <div class="row g-3 mt-2">
                @include('admin.programs.input-component', [
                'name' => 'annual_fee',
                'label' => 'Annual Fee',
                'type' => 'number',
                'placeholder' => 'Enter Annual Fee',
                'id' => 'annual_feeId',
                'value' => old('annual_fee')
                ])
                @include('admin.programs.input-component', [
                'name' => 'cost_per_session',
                'label' => 'Cost per Session',
                'type' => 'number',
                'placeholder' => 'Enter Cost per Session',
                'id' => 'cost_per_sessionId',
                'value' => old('cost_per_session')
                ])
                @include('admin.programs.input-component', [
                'name' => 'renewal_date',
                'label' => 'Renewal Date',
                'type' => 'text',
                'placeholder' => 'dd/mm',
                'id' => 'renewal_dateId',
                'value' => old('renewal_date')
                ])
            </div>

            <label class="form-label mt-2">GST? +10%</label>
            <div class="btn-group-toggle d-flex gap-2 flex-wrap">
                <input type="radio" class="btn-check" name="gst_registered" id="yes" value="yes" autocomplete="off" {{ old('gst_registered') == 'yes' ? 'checked' : '' }}>
                <label class="btn btn-outline-primary rounded-pill" for="yes">Yes</label>

                <input type="radio" class="btn-check" name="gst_registered" id="no" value="no" autocomplete="off" {{ old('gst_registered') == 'no' ? 'checked' : '' }}>
                <label class="btn btn-outline-primary rounded-pill" for="no">No</label>
            </div>
        </div>

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary mindway-btn-blue">
                {{ $type == 2 ? 'Create Trial Program' : 'Create Active Program' }}
            </button>
        </div>
    </div>
</form>

<div class="modal fade" id="add-department" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Department Name</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="departmentNameInput" class="form-control" placeholder="Enter Department Name">
            </div>
            <div class="modal-footer">
                <button id="addDepartmentButton" type="button" class="btn btn-primary">Add</button>
            </div>
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