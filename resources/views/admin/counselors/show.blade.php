@extends('layouts.app')

@section('selected_menu', 'active')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Welcome {{ $user->counsellorDetail?->name }}</h2>
        </div>
        <h6>Upcoming Sessions ({{$upcomingBookings->count()}})</h6>

        <div class="table-responsive">
            <table class="table text-nowrap mb-0 align-middle">
                <tbody id="customersTable">
                    @if($upcomingBookings->isNotEmpty())
                    @foreach($upcomingBookings as $booking)
                    <tr style="border-bottom: none;">
                        <td colspan="3" style="padding: 0; border: none;">
                            <div class="card" style="border-radius: 8px; margin: 15px 15px 15px 15px;">
                                <div class="card-body">
                                    <div class="row">
                                        <!-- User Information -->
                                        <div class="col-md-3" style="border-right: 4px solid #D4D4D4;">
                                            <h5 class="fw-semibold">{{ optional($booking->user)->name ?? 'N/A' }}</h5>
                                            <p class="fw-bold mb-1">{{ optional($booking->user)->email ?? 'Email not provided' }}</p>
                                            <p class="fw-bold mb-0">{{ optional($booking->user)->max_session ?? 0 }} Session(s) Remaining</p>
                                        </div>
                                        <!-- Date & Time -->
                                        <div class="col-md-3" style="border-right: 4px solid #D4D4D4;">
                                            <p></p>

                                            <h5 class="fw-semibold" style="display: block;width: 100%;max-width: 300px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">Date & Time {{$booking?->counselor?->timezone}}</h5>
                                            <p class="fw-bold mb-0">
                                                {{ optional($booking->slot)->start_time?->setTimezone($timezone)->format('Y-m-d') ?? 'No date available' }}
                                                {{ optional($booking->slot)->start_time?->setTimezone($timezone)->format('H:i') ?? 'No time available' }}
                                            </p>
                                        </div>
                                        <!-- Actions -->
                                        <div class="col-md-3" style="border-right: 4px solid #D4D4D4;">
                                            <a class="btn btn-primary mindway-btn" href="{{ route('counsellor.session.cancel', ['booking_id' => $booking->id, 'customer_id' => $booking->user_id]) }}">
                                                Cancel
                                            </a>
                                            <a data-bs-toggle="modal" data-bs-target="#rebookSessionModal" class="btn btn-primary mindway-btn" href="{{ route('counsellor.session.rebook', ['booking_id' => $booking->id]) }}">
                                                Rebook
                                            </a>
                                            <br>
                                            <a style="background-color: #688edc !important; color: white !important; margin-top: 10px;"
                                                class="btn btn-primary add-session-btn mindway-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#addSessionModal"
                                                data-id="{{ $booking?->user_id }}"
                                                data-counselor_id="{{ $booking?->counselor_id }}"
                                                data-slot_id="{{ $booking?->slot_id }}"
                                                data-name="{{ $booking?->counselor?->name }}"
                                                data-program_id="{{ $booking?->brevoUser?->program_id }}"
                                                data-customer_name="{{ $booking?->user?->name }}">
                                                Log Session
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <p style="margin: unset;">
                                                @if($booking->communication_method == 'Video Call')
                                                Video Call Chosen
                                                <br>
                                                <a target="_blank" href="{{$booking->meeting_link}}"
                                                    class="btn btn-primary mindway-btn">
                                                    JOIN MEETING
                                                </a>
                                                @else
                                                Phone Call Chosen
                                                <br>
                                                <strong>Call: {{$booking?->user?->phone}}</strong>
                                                @endif
                                            </p>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="3" class="text-center">
                            <h5 class="fw-semibold">No upcoming bookings available.</h5>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="row mt-4">
            <div class="col-md-12 d-flex justify-content-center">
                {{ $upcomingBookings->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
<hr>
<div class="sessions-header">
    <h2>All Counselling Sessions</h2>
</div>
<input type="hidden" id="user_id" name="user_id" value="{{$user->id}}">
<div class="table-responsive">
    <table class="table text-nowrap mb-0 align-middle" width="100%" id="Yajra-dataTable">
        <thead class="text-dark fs-4">
            <tr>
                <th>
                    <h6>ID</h6>
                </th>
                <th>
                    <h6>Name</h6>
                </th>
                <th>
                    <h6>Company Name</h6>
                </th>
                <th>
                    <h6>Email</h6>
                </th>
                <th>
                    <h6>Counsellor Name</h6>
                </th>
                <th>
                    <h6>Session Date</h6>
                </th>
                <th>
                    <h6>Session Type</h6>
                </th>
                <th>
                    <h6>Max Session</h6>
                </th>
            </tr>
        </thead>
    </table>
</div>


@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/dashboard/css/counsellor-manage.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/dashboard/js/counsellor-manage.js') }}"></script>
@endpush