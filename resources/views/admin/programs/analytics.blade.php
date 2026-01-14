@extends('layouts.app')

@section('selected_menu', 'active')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/dashboard/program/css/analytics-page.css') }}">

    <div class="row">
        <div class="col-10 offset-1">

            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <img style="object-fit: contain;" height="46px" width="130px" class="popup"
                    src="{{ asset('storage/logo/' . $user?->programDetail?->logo) }}" alt="{{ $user?->programDetail?->company_name }} Logo">
                </div>
                <div class="trial-info">
                    @if ($is_trial)
                        <p><b>On Free Trial:</b> <span>{{ $leftDays }} days left of trial</span></p>
                    @endif
                </div>
            </div>


            <div class="main-content">
                <h1><strong>{{ $user->company_name }} Analytics</strong></h1>
                <h4 class="main-content-subtitle">See platform and session insights</h4>

                @if (isset($departments) && is_iterable($departments) && count($departments) > 0)
<nav class="navbar navbar-expand-lg navbar-light bg-white" style="overflow-x: hidden; width: 100%;">
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto tabs-container">
            <!-- Active Tab -->
            <li class="nav-item {{ !request()->has('department') ? 'active-tab' : '' }}">
                <a class="nav-link nav-link-base {{ !request()->has('department') ? 'nav-link-active' : 'nav-link-default' }}"
                    href="/manage-program/view-analytics">All</a>
            </li>
            @foreach ($departments as $depart)
            <li class="nav-item {{ request()->get('department') == $depart->id ? 'active-tab' : '' }}">
                <a class="nav-link nav-link-base {{ request()->get('department') == $depart->id ? 'nav-link-active' : 'nav-link-default' }}"
                    href="/manage-program/view-analytics?department={{ $depart->id }}">{{ $depart->name }}</a>
            </li>
            @endforeach
        </ul>
    </div>
</nav>
                @endif
                <div class="text-with-line mt-3 mb-2"><strong>Utilisation</strong></div>

                <div class="row">
                    <div class="col-4"> <!-- Flexibly adjusts width -->
                        <div class="card licenses-card" style="height: 39px">
                            <div class="card-body d-flex justify-content-center align-items-center p-4">
                                <span>Licenses </span> <span>{{ $user?->programDetail?->max_lic }}</span>
                            </div>
                        </div>
                    </div>

                   
                <div class="col-8">
                    <div class="card adoption-rate-card">
                        <div class="card-body d-flex justify-content-center align-items-center p-4">
                            <span>Overall Adoption Rate</span> <span>{{number_format($adoptionRate, 2) }} %</span>
                        </div>
                    </div>
                </div>
            </div>

                {{-- {{ ($totalCustomersCount / 1000) * 100 }} --}}
                            <div class="card w-100" style="width:576px; height:127px;">
                <div class="card-body p-4">
                    <h6>Overall Adoption</h6>
                    <h4><strong>{{ $adoptedUsers }} Employees</strong></h4>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted" id="min-value">0</span>
                        <div class="progress w-100 mx-3">
                            <div class="progress-bar" role="progressbar" style="width:{{ round($adoptionRate) }}%;"
                    aria-valuenow="{{ $adoptedUsers }}"
                    aria-valuemin="0"
                    aria-valuemax="{{ $allUsers->count() }}"></div>
                        </div>
                        <span class="text-muted" id="max-value">{{ $allUsers->count() }}</span>
                    </div>
                </div>
            </div>

                <div class="card w-100">
                    <div class="card-body p-4">
                        <h3 style="display: inline;">Growth of Program</h3>
                        <p class="growth-program-header-subtitle">Past 12 months</p>
                        <p class="mt-2">This shows the total users enrolled in the program</p>
                        <div class="chart-container">
                            <canvas id="growthProgramChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="text-with-line mt-3 mb-3"><strong>Sessions</strong></div>
                <div class="row">
                    <div class="col me-3"> <!-- Flexibly adjusts width -->
                        <div class="card">
                            <div class="card-body d-flex justify-content-center align-items-center p-4">
                                <b>Total Sessions {{ $totalSessions }}</b>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card">
                            <div class="card-body d-flex justify-content-center align-items-center p-4">
                                <b>Unique Session Users {{ $newUserCount }}</b>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card w-100">
                    <div class="card-body p-4">
                        <h3 style="display: inline;">Growth of Sessions</h3>
                        <p class="growth-program-header-subtitle">Past 6 months</p>
                        <p class="mt-2">This shows session conducted</p>
                        <div class="chart-container">
                            <canvas id="sessionsChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card w-100">
                    <div class="card-body p-4">
                        <h3 style="display: inline;">Reason for Sessions</h3>
                        <br>
                        <br>
                        <div class="progress custom-height">
                            <div class="progress-bar work" role="progressbar"
                                style="width: {{ $percentageData['workReasonsPercentage'] }}%;" aria-valuenow="30"
                                aria-valuemin="0" aria-valuemax="100">
                                Work Related {{ ceil($percentageData['workReasonsPercentage']) }}%
                            </div>

                            <div class="progress-bar personal" role="progressbar"
                                style="width: {{ $percentageData['personRelatedPercentage'] }}%;" aria-valuenow="20"
                                aria-valuemin="0" aria-valuemax="100">
                                Personal Related {{ floor($percentageData['personRelatedPercentage']) }}%
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card w-100">
                    <div class="card-body p-4">
                        <h3>Breakdown of Sessions</h3>
                        <p class="mt-2">See the common threads to why work related-sessions have been conducted</p>
                        <div class="chart-container">
                            <canvas id="breakdownChart"></canvas>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    @endsection

    @section('js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

        <script>
            const growthData = @json($growthData);
            const labels = @json($labels);
            const labelsSession = @json($labelsSession);
            const growthDataSession = @json($growthDataSession);
            const sessionReasonLabel = @json($sessionReasonLabel);
            const sessionReasonData = @json($sessionReasonData);
        </script>

        <script src="{{ asset('assets/dashboard/program/css/analytics-page.js') }}"></script>
    @endsection