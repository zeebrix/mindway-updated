@extends('layouts.app')

@section('selected_menu', 'active')

@section('content')
    <style>
        .nav-item .nav-link { padding: 10px 15px; border-radius: 5px; transition: background-color 0.3s ease; }
        .nav-item .nav-link:hover { background-color: unset; }
        .active-tab .nav-link { background-color: unset; color: #688EDC; font-weight: 700; border-radius: 5px; }
    </style>
    <div class="w-100">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title fw-bolder" style="color:#000000">Requested Sessions</h5>
            </div>

            <div class="mb-4 col-12">
                <nav class="navbar navbar-expand-lg navbar-light bg-white">
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item {{ $status == 'pending' ? 'active-tab' : '' }}" style="margin-right: 10px;">
                                <a class="nav-link fw-bolder" href="{{ route('admin.request-sessions.index', ['status' => 'pending']) }}">Pending</a>
                            </li>
                            <li class="nav-item {{ $status == 'accepted' ? 'active-tab' : '' }}" style="margin-right: 10px;">
                                <a class="nav-link fw-bolder" href="{{ route('admin.request-sessions.index', ['status' => 'accepted']) }}">Accepted</a>
                            </li>
                            <li class="nav-item {{ $status == 'denied' ? 'active-tab' : '' }}">
                                <a class="nav-link fw-bolder" href="{{ route('admin.request-sessions.index', ['status' => 'denied']) }}">Denied</a>
                            </li>
                        </ul>
                    </div>
                </nav>
                <hr class="p-0 m-0" style="border: 1px solid #AEAEAE;">
            </div>

            @if (session()->has('message'))<div class="alert alert-success">{{ session('message') }}</div>@endif
            @if ($errors->any())
                <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            {{-- The reusable datatable component --}}
            @include('components.datatable', [
                'tableId' => $tableId,
                'title' => '',
                'dataTableConfig' => $dataTableConfig,
            ])
        </div>
    </div>
    @if ($status == 'pending')
        @include('admin.request-sessions.review-modal')
    @else
        @include('admin.request-sessions.display-modal')
    @endif
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Loader for form submission
            $('#requestedModal form').on('submit', function() {
                $('#requestSessionLoader').fadeIn();
            });

            // Reusable modal instance
            const reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'));

            // Event delegation for review button clicks
            $(document).on('click', '.review-btn', function(e) {
                e.preventDefault();
                var url = $(this).attr('href'); // This now points to the 'show' route
                
                $.get(url, function(data) {
                    if(data.success) {
                        // --- Populate Modal Fields ---
                        $('#modalRequestId').text(data.request_id || 'N/A');
                        $('#modalClientName').text(data.client_name || 'N/A');
                        $('#modalClientEmail').text(data.client_email || 'N/A');
                        $('#modalClientId').text(data.client_id || 'N/A');
                        $('#modalCounselorName').text(data.counselor_name || 'N/A');
                        $('#modalReasons').text(data.reasons || 'N/A');
                        
                        // --- Set Form Actions ---
                        // The forms now point to the new, specific routes
                        $('#approveForm').attr('action', `/admin/request-sessions/${data.request_id}/approve`);
                        $('#denyForm').attr('action', `/admin/request-sessions/${data.request_id}/deny`);

                        // --- Handle Display Logic ---
                        const status = "{{ $status }}";
                        if (status === 'pending') {
                            $('#modalRequestedDate').text(data.requested_date || 'N/A').closest('h6').show();
                            $('#modalApprovedDate').closest('h6').hide();
                            $('#modalDeniedDate').closest('h6').hide();
                            $('.modal-footer-actions').show(); // Show Approve/Deny buttons
                        } else if (status === 'accepted') {
                            $('#modalApprovedDate').text(data.approved_date || 'N/A').closest('h6').show();
                            $('#modalRequestedDate').closest('h6').hide();
                            $('#modalDeniedDate').closest('h6').hide();
                            $('.modal-footer-actions').hide(); // Hide buttons
                        } else if (status === 'denied') {
                            $('#modalDeniedDate').text(data.denied_date || 'N/A').closest('h6').show();
                            $('#modalRequestedDate').closest('h6').hide();
                            $('#modalApprovedDate').closest('h6').hide();
                            $('.modal-footer-actions').hide(); // Hide buttons
                        }
                        
                        // Pre-check the requested number of sessions
                        if(data.requested_days) {
                            $(`#session-count-${data.requested_days}`).prop('checked', true);
                        }
                        
                        reviewModal.show();
                    }
                }).fail(function() {
                    alert('Failed to load request details. Please try again.');
                });
            });
        });
    </script>
@endpush
