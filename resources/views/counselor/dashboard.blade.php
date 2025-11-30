@extends('layouts.app')

@section('selected_menu', 'active')

@section('content')

<div class="row">
    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2>Welcome {{ $counselor->name }} 👋</h2>
        </div>

        <h6 class="fw-bold">Upcoming Sessions ({{ $upcomingBookings->count() }})</h6>

        <div class="table-responsive">
            <table class="table text-nowrap mb-0 table-no-border">
                <tbody>
                    @forelse($upcomingBookings as $booking)
                    <tr>
                        <td colspan="3">
                            <div class="card card-session">
                                <div class="card-body">
                                    <div class="row">

                                        {{-- User Information --}}
                                        <div class="col-md-3 border-right-gray">
                                            <h5 class="fw-semibold">{{ $booking->user->name ?? 'N/A' }}</h5>
                                            <p class="fw-bold mb-1">{{ $booking->user->email ?? 'Email not provided' }}</p>

                                            @if($booking->user->preferred_email)
                                                <div class="d-flex flex-wrap">
                                                    <span class="text-success fw-bold me-1">Preferred:</span>
                                                    <p class="mb-0 fw-semibold">{{ $booking->user->preferred_email }}</p>
                                                </div>
                                            @endif

                                            <p class="fw-bold mb-0">{{ $booking->user->max_session ?? 0 }} Session(s) Remaining</p>
                                        </div>

                                        {{-- Date & Time --}}
                                        <div class="col-md-3 border-right-gray">
                                            <h5 class="fw-semibold text-ellipsis">
                                                Date & Time {{ $booking->counselor->timezone }}
                                            </h5>

                                            <p class="fw-bold mb-0">
                                                {{ optional($booking->slot->start_time)->setTimezone($timezone)->format('Y-m-d H:i') }}
                                            </p>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="col-md-3 border-right-gray">

                                            {{-- Cancel Button --}}
                                            <a href="javascript:void(0);"
                                               class="btn btn-primary mindway-btn js-cancel-session mt-2"
                                               data-url="{{ route('counselor.session.cancel', ['booking_id' => $booking->id, 'customer_id' => $booking->user_id, 'customer_timezone' => $booking->user->timezone]) }}"
                                               data-user-name="{{ $booking->user->name }}"
                                               data-session-date="{{ $booking->slot->start_time->setTimezone($timezone)->format('F j, Y \a\t g:i A') }}">
                                                Cancel
                                            </a>

                                            {{-- Log Session --}}
                                            <a class="btn btn-log-session mindway-btn"
                                               data-bs-toggle="modal"
                                               data-bs-target="#addSessionModal"
                                               data-id="{{ $booking->user_id }}"
                                               data-counselor_id="{{ $booking->counselor_id }}"
                                               data-slot_id="{{ $booking->slot_id }}"
                                               data-program_id="{{ $booking->brevoUser?->program_id }}">
                                                Log Session
                                            </a>
                                        </div>

                                        {{-- Meeting --}}
                                        <div class="col-md-3">
                                            @if($booking->communication_method === 'Video Call')
                                                <p>Video Call Chosen</p>
                                                <a target="_blank" href="{{ $booking->meeting_link }}" class="btn btn-primary mindway-btn">
                                                    JOIN MEETING
                                                </a>
                                            @else
                                                <p>Phone Call Chosen</p>
                                                <strong>Call: {{ $booking->user->phone }}</strong>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="3" class="text-center">
                            <h5 class="fw-semibold">No upcoming bookings available.</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="row mt-4">
            <div class="col-md-12 d-flex justify-content-center">
                {{ $upcomingBookings->links('pagination::bootstrap-4') }}
            </div>
        </div>

        @include('counselor.session.modal.add')
        @include('counselor.session.modal.rebook')

    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/dashboard/js/dashboard.js') }}"></script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/dashboard/js/dashboard.css') }}">
@endpush
