@extends('layouts.app')
@section('selected_menu', 'active')

@section('content')

<div class="row">
    <div class="col-10 offset-1">

        <div class="card-body p-4">
            <h2 class="card-title mb-4 fw-bold">My Availability</h2>

            <!-- Timezone selector -->
            <div class="mb-4 d-flex align-items-center">
                <span class="fw-semibold me-2">Time Zone:</span>
                <span class="me-2">
                    <span id="selected-timezone">{{ $currentTimezone }}</span> —
                    <a href="#" data-bs-toggle="modal" data-bs-target="#timezoneModal">change</a>
                </span>
            </div>

            <!-- Availability container -->
            <div id="availability-container"></div>

            <div class="mt-4">
                <button class="btn btn-primary w-50" id="saveButton">Save Changes</button>
            </div>
        </div>

        @include('counselor.components.modal.timezone')

    </div>
</div>

@endsection

@push('styles')
<meta name="counselor-id" content="{{ $counselor->id }}">
<link rel="stylesheet" href="{{ asset('assets/dashboard/counselor/cs/availability.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/dashboard/counselor/js/availability.js') }}"></script>
@endpush
