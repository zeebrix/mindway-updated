@extends('layouts.app')
@section('selected_menu', 'active')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/dashboard/css/availability.css') }}">
@endpush

@section('content')

<div class="row">
    <div class="col-10 offset-1">

        <div class="card-body p-4">

            <h2 class="card-title mb-4 fw-bold">My Availability</h2>

            <div class="mb-4 d-flex align-items-center">
                <strong class="me-2">Time Zone:</strong>
                <span>
                    <span id="selected-timezone">{{ $currentTimezone }}</span> —
                    <a href="#" class="timezone-link" data-bs-toggle="modal" data-bs-target="#timezoneModal">
                        Change
                    </a>
                </span>
            </div>

            <div id="availability-container"></div>

            <div class="mt-4">
                <button class="btn btn-primary w-50" id="saveButton">Save Changes</button>
            </div>

        </div>
        <input type="hidden" id="counselor_id" value="{{ $user->id }}">

        <div class="modal fade" id="timezoneModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3">

                    <div class="modal-header">
                        <h5 class="modal-title">Select Time Zone</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-0">
                        <div class="timezone-search">
                            <input type="text" class="form-control" id="timezone-search" placeholder="Search time zones...">
                        </div>
                        <div class="timezone-list" id="timezone-list"></div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/dashboard/js/availability.js') }}"></script>
@endpush