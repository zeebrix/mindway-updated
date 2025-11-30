@extends('layouts.app')
@section('selected_menu', 'active')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 fw-bold">Session History</h2>

    @forelse ($bookings as $month => $sessions)
        <div class="card session-card mb-4">

            {{-- Card Header --}}
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ $month }}</span>
                <span class="text-muted">{{ $sessions->count() }} sessions</span>
            </div>

            {{-- Card Body --}}
            <div class="card-body p-0">

                {{-- Table Header --}}
                <div class="session-row session-head">
                    <div>Client</div>
                    <div class="text-end">Date</div>
                </div>

                {{-- Table Rows --}}
                @foreach ($sessions as $booking)
                    <div class="session-row">
                        <div>
                            <div class="session-client-name">{{ $booking->name ?? 'N/A' }}</div>
                            <div class="session-client-email">{{ $booking->email ?? 'N/A' }}</div>
                        </div>

                        <div class="session-date">
                            {{ optional($booking->created_at)->format('M j') ?? 'N/A' }}
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

    @empty
        <p class="text-muted text-center py-5">No session history available.</p>
    @endforelse
</div>
@endsection
@push('styles')
<script src="{{ asset('assets/dashboard/counselor/css/session-history.css') }}"></script>

@endpush
