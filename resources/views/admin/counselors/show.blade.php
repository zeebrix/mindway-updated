@extends('layouts.app')

@section('selected_menu', 'active')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Welcome {{ $counselor->name }}</h2>
            </div>
            <h6>Upcoming Sessions ({{$upcomingBookings->count()}})</h6>

            <div class="table-responsive">
                {{-- This is the exact loop and card structure from your original file --}}
                @if($upcomingBookings->isNotEmpty())
                    @foreach($upcomingBookings as $booking)
                        <div class="card" style="border-radius: 8px; margin-bottom: 15px;">
                            <div class="card-body">
                                <div class="row">
                                    {{-- User Info --}}
                                    <div class="col-md-3" style="border-right: 4px solid #D4D4D4;">...</div>
                                    {{-- Date & Time --}}
                                    <div class="col-md-3" style="border-right: 4px solid #D4D4D4;">...</div>
                                    {{-- Actions --}}
                                    <div class="col-md-3" style="border-right: 4px solid #D4D4D4;">...</div>
                                    {{-- Meeting Link --}}
                                    <div class="col-md-3">...</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center"><h5 class="fw-semibold">No upcoming bookings available.</h5></div>
                @endif
            </div>

            <div class="row mt-4"><div class="col-md-12 d-flex justify-content-center">{{ $upcomingBookings->links('pagination::bootstrap-4') }}</div></div>
        </div>
    </div>
    <hr>  

    {{-- The "All Counselling Sessions" datatable can also be included here if needed --}}
@endsection

@push('scripts')
    {{-- Your JavaScript for the modals on this page --}}
@endpush
