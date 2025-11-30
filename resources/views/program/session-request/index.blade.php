@extends('layouts.app')

@section('selected_menu', 'active')

{{-- Link the external stylesheet in the head section --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/dashboard/program/css/request-session.css') }}">
@endpush

@section('content')
<div class="w-100 page-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="card-title fw-bolder">Requested Sessions</h5>
    </div>

    {{-- Simplified and cleaner tab navigation --}}
    <div class="mb-4">
        <ul class="nav tabs-container">
            @php
                $currentStatus = request('status', 'pending'); // Default to 'pending'
                $tabs = ['pending', 'accepted', 'denied'];
            @endphp

            @foreach ($tabs as $status)
                <li class="nav-item tab-item {{ $currentStatus == $status ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('program.requests.view', ['status' => $status]) }}">
                        {{ ucfirst($status) }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Session Messages and Errors --}}
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

    {{-- Responsive Table --}}
    <div class="table-responsive">
        <table class="table request-table">
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Date Requested</th>
                    <th>Sessions</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr>
                        <td class="request-id">#{{ $request->id }}</td>
                        <td>{{ \Carbon\Carbon::parse($request->request_date)->format('d/m/Y') }}</td>
                        <td>
                            @if($currentStatus == 'pending')
                                {{ $request->request_days }} Recommended
                            @elseif($currentStatus == 'accepted')
                                {{ $request->request_days }} Approved
                            @else
                                0
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('reviewSessionRequest', ['id' => $request->id]) }}"
                               class="review-btn"
                               data-status="{{ $currentStatus }}">
                                Review Request
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">No {{ $currentStatus }} requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($requests->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $requests->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>

{{-- Include Modals based on status --}}
@if ($currentStatus == 'pending')
    @include('program.session-request.review')
@else
    @include('program.session-request.onlydisplay')
@endif
@endsection

@push('scripts')
    {{-- Link the external JavaScript file before the closing body tag --}}
    <script src="{{ asset('assets/dashboard/program/js/request-session.js') }}"></script>
@endpush
