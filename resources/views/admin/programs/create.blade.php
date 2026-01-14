@extends('layouts.app')

@section('selected_menu', 'active')

@section('content')
<h1 class="fw-bolder mb-2">{{ $type == 1 ? 'Create New Active Program' : 'Create Trial Program' }}</h1>

<h4 class="fw-bolder mb-2">Company Details</h4>

@include('layouts.partials.alerts')

<form action="{{ route('admin.programs.store') }}" method="POST" enctype="multipart/form-data" class="program-form needs-validation" novalidate>
    @csrf
    <input type="hidden" name="program_type" value="{{ $type }}">

    <div class="row g-3">


        @include('admin.programs.input-component', [
        'name' => 'company_name',
        'label' => 'Company Name',
        'type' => 'text',
        'placeholder' => 'Enter Company Name',
        'is_required' => true,
        'id' => 'company_name',
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

        @include('admin.programs.upload-component', [
        'id' => 'logo',
        'name' => 'logo',
        'label' => 'Upload Logo',
        'existing_image' => null,
        'required' => true
        ])



        <div class="col-12">
            <label class="form-label">Employees Visible?</label>
            <div class="btn-group-toggle d-flex gap-2 flex-wrap" id="allow-employees-group">
                <input type="radio" class="btn-check" name="allow_employees" id="yes-employee" value="yes" autocomplete="off">
                <label class="btn btn-outline-primary rounded-pill" for="yes-employee">Yes</label>

                <input type="radio" class="btn-check" name="allow_employees" id="no-employee" value="no" autocomplete="off">
                <label class="btn btn-outline-primary rounded-pill" for="no-employee">No</label>
            </div>
            <div class="invalid-feedback">
                Please select if employees are visible.
            </div>
        </div>



        <span class="fw-bolder mb-3 d-block">Assign Admin User</span>

        @include('admin.programs.input-component', [
        'name' => 'full_name',
        'label' => 'Full Name',
        'type' => 'text',
        'placeholder' => 'Ryder Mckenzie',
        'is_required' => true,
        'id' => 'full_name',
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

@include("admin.programs.modal.add-department");
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/dashboard/css/program-form.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/dashboard/js/program-form.js') }}"></script>
@endpush