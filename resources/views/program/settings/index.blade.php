@extends('layouts.app')

@section('selected_menu', 'active')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/dashboard/program/css/program-settings.css') }}">
@endpush

@section('content')
<div class="settings-page">
    {{-- Header --}}
    <header class="settings-header">
        <img class="settings-header__logo" src="{{ asset('storage/logo/' . $user->ProgramDetail->logo) }}" alt="{{ $user->ProgramDetail->company_name }} Logo">
        @if ($is_trial)
            <p class="settings-header__trial-status">On Free Trial: {{ $leftDays }} days left</p>
        @endif
    </header>

    <h4>Settings</h4>

    {{-- Tab Navigation --}}
    <nav class="settings-tabs">
        <div class="nav">
            <a class="nav-link active" id="overviewTab" href="#overview">Overview</a>
            @if (!$is_trial)
                <a class="nav-link" id="planPaymentTab" href="#plan_payment">Plan & Payment</a>
            @endif
        </div>
    </nav>

    {{-- Success/Error Messages --}}
    @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Tab Content: Overview --}}
    <div id="overview" class="tab-pane">
        {{-- Organization Name Card --}}
        <div class="settings-card">
            <div class="settings-card__content">
                <label for="company_nameId" class="settings-card__label">Organization Name</label>
                <input type="text" class="form-control settings-card__input" id="company_nameId" name="company_name" value="{{ $user->ProgramDetail->company_name }}" readonly>
            </div>
            <div id="edit-name-container">
                <i id="edit-name-btn" class="ti ti-pencil settings-card__action--editable" title="Edit Name"></i>
            </div>
        </div>

        {{-- Access Code Card --}}
        <div class="settings-card">
            <div class="settings-card__content">
                <label class="settings-card__label">Access Code (3-8 characters)</label>
                <h5 class="settings-card__value mb-0">{{ $user->ProgramDetail->code }}</h5>
            </div>
            <span class="settings-card__action">Contact us to change</span>
        </div>

        <div class="row">
            {{-- Upload Logo Card --}}
            <div class="col-md-6">
                <div class="settings-card upload-card" id="uploadLogoTrigger">
                    <div class="settings-card__content">
                        <h5 class="settings-card__value">Upload Logo</h5>
                    </div>
                    <img class="upload-card__icon" src="{{ asset('mw-1/assets/images/upload.png') }}" alt="Upload icon">
                    <input type="file" id="uploadLogoInput" data-upload-url="{{ route('program.setting.save', ['id' => $user->ProgramDetail->id]) }}" style="display: none;" accept="image/*" />
                </div>
            </div>
            {{-- License Card --}}
            <div class="col-md-6">
                <div class="settings-card">
                    <div class="settings-card__content">
                        <h5 class="settings-card__value">Licenses: {{ $user->ProgramDetail->max_lic }}</h5>
                    </div>
                    <span class="settings-card__action">Contact us to change</span>
                </div>
            </div>
        </div>

        {{-- 2FA Settings --}}
        <form method="post" action="{{ route('program.setting.save') }}" class="two-factor-section">
            @csrf
            <div class="mb-4">
                <label for="enable_2fa" class="two-factor-section__toggle">
                    <input type="checkbox" id="enable_2fa" name="enable_2fa" {{ $user->is_2fa_enabled ? 'checked' : '' }}>
                    <span>Enable Two-Factor Authentication</span>
                </label>
            </div>

            <div id="2fa-setup" style="display: {{ $user->is_2fa_enabled ? 'block' : 'none' }};">
                <p>Scan the barcode with your authenticator app. Alternatively, use the code: <strong id="2fa-secret">{{ $secret }}</strong></p>
                @if ($qrCodeUrl = $qrCodeUrl ?? session('qrCodeUrl'))
                    <div id="qr-code-container">{!! QrCode::size(200)->generate($qrCodeUrl) !!}</div>
                @endif
            </div>

            <button type="submit" class="btn btn-primary mindway-btn-blue">Save Settings</button>
        </form>
    </div>

    {{-- Tab Content: Plan & Payment --}}
    <div id="plan_payment" class="tab-pane" style="display: none;">
        @php
            $planDetails = [
                'Plan Selected' => ($plan?->plan_type == 1 ? 'Pay As You Go' : ($plan?->plan_type == 2 ? 'Standard' : 'Premium')),
                'Subscription Fee' => '$ ' . ($plan?->annual_fee ?? 'N/A') . '/Year' . ($plan?->gst_registered ? ' + GST' : ''),
                'Session Cost' => '$ ' . ($plan?->cost_per_session ?? 'N/A') . '/Session' . ($plan?->gst_registered ? ' + GST' : ''),
                'Session Limit' => ($Program->max_session ?? 'N/A') . ' per employee, per year',
                'Renewal Date' => $plan?->renewal_date ? \Carbon\Carbon::parse($plan->renewal_date)->format('F j, Y') : 'N/A',
            ];
        @endphp

        @foreach ($planDetails as $label => $value)
        <div class="settings-card">
            <div class="settings-card__content">
                <label class="settings-card__label">{{ $label }}</label>
                <h5 class="settings-card__value mb-0">{{ $value }}</h5>
            </div>
            <span class="settings-card__action">Contact us to change</span>
        </div>
        @endforeach

        <div class="text-center mt-4">To change any settings, please contact your account manager.</div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/dashboard/program/js/program-settings.js') }}"></script>
@endpush
