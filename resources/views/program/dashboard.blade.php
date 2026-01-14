@extends('layouts.app')


@push('styles')
<link rel="stylesheet" href="{{ asset('assets/dashboard/css/program-dashboard.css') }}" media="all">
@endpush
@section('content')
<div class="row">
    <div class="col-10 offset-1">

        <!-- Header Section -->
        <div class="dashboard-header mb-2">
            <div class="header-logo">
                <img class="logo-container popup"
                    src="{{ asset('storage/' . $program?->programDetail?->logo) }}"
                    alt="{{ $program->name }} Logo"
                    data-animate="true">
            </div>
            <div class="header-trial-info">
                @if ($is_trial)
                <p>
                    <span class="trial-badge" data-metric="trial-badge">On Free Trial:</span>
                    <span class="trial-days" data-metric="trial-days">{{ $leftDays }} days left of trial</span>
                </p>
                @endif
            </div>
        </div>

        <!-- Welcome Section -->
        <div class="welcome-section mb-2">
            <h2 class="welcome-title" data-metric="welcome-name" data-animate="true">
                Welcome {{ $program?->name }} 👋
            </h2>

            <h4 class="company-title" data-animate="true">
                EAP Program for {{ $program?->programDetail?->company_name }}
            </h4>

            <p class="company-subtitle"></p>
        </div>

        <!-- Utilisation Header -->
        <div class="text-with-line utilisation-header">
            <strong>Utilisation</strong>
        </div>

        <!-- Metrics Container -->
        <div class="row">
            <!-- Licenses Card -->
            <div class="col-6">
                <div class="card card-shadow metric-card" data-animate="true">
                    <div class="card-body">
                        <span class="metric-label">Licenses</span>
                        <span class="metric-value" data-metric="licenses">{{ $program?->programDetail?->max_lic }}</span>
                    </div>
                </div>
            </div>

            <!-- Adoption Rate Card -->
            <div class="col-6">
                <div class="card card-shadow metric-card" data-animate="true">
                    <div class="card-body">
                        <span class="metric-label">Overall Adoption Rate</span>
                        <span class="metric-value" data-metric="adoption-rate">

                            {{ number_format($adoptionRate, 2) }} %
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Adoption Card -->
        <div class="card card-shadow adoption-card section-spacing" data-animate="true">
            <div class="card-body">
                <h6 class="adoption-title">Overall Adoption</h6>
                <h4 class="adoption-count">
                    <strong>
                        <span data-metric="adopted-count">{{ $adoptedUsers }}</span> Employees
                    </strong>
                </h4>
                <div class="adoption-progress-container">
                    <span class="adoption-progress-label" data-metric="min-value">0</span>
                    <div class="adoption-progress-bar">
                        <div class="progress">
                            <div class="progress-bar"
                                role="progressbar"
                                data-metric="adoption-progress"
                                style="width: {{ round($adoptionRate) }}%;"
                                aria-valuenow="{{ $adoptedUsers }}"
                                aria-valuemin="0"
                                aria-valuemax="{{ $allUsers->count() }}">
                            </div>
                        </div>
                    </div>
                    <span class="adoption-progress-label" data-metric="max-value">{{ $allUsers->count() }}</span>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
@push('scripts')
<script src="{{ asset('assets/dashboard/js/program-dashboard.js') }}" defer></script>
@endpush