@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/dashboard/counselor/css/session-history.css') }}">

@endpush
@section('selected_menu', 'active')

@section('content')

<div class="container py-4">
    <h2 class="mb-4 fw-bold">Session History</h2>

    @forelse ($bookings as $month => $sessions)
    <div class="card session-card">
        <div class="card-header">
            {{ $month }} ({{ $sessions->count() }} sessions)
        </div>

        <div class="card-body">

            {{-- HEADER ROW --}}
            <div class="session-row session-header">
                <div class="col-client">Client</div>
                <div class="col-status">Status</div>
                <div class="col-date">Date</div>
                <div class="col-action text-end">Action</div>
            </div>

            {{-- SESSION ROWS --}}
            @foreach ($sessions as $booking)
            <div class="session-row">
                <div class="col-client">
                    <div class="session-client-name">{{ $booking->customer->name ?? 'N/A' }}</div>
                    <div class="session-client-email">{{ $booking->customer->email ?? 'N/A' }}</div>
                </div>

                <div class="col-status">
                    <span>
                        {{
                            match($booking->status) {
                                'no_show' => 'No-Show',
                                'late_cancellation' => 'Late Cancellation',
                                default => 'Completed'
                            }
                        }}
                    </span>
                </div>

                <div class="col-date">
                    {{ \Carbon\Carbon::parse($booking->created_at)->format('M j') }}
                </div>

                <div class="col-action text-end">
                    <button
                        class="btn delete-session-btn mindway-btn-blue btn-sm"
                        data-bs-toggle="modal"
                        data-id="{{ $booking->id }}"
                        data-bs-target="#deleteSessionModal"
                        data-name="{{ $booking->name }}"
                        data-email="{{ $booking->email }}"
                        data-date="{{ \Carbon\Carbon::parse($booking->created_at)->format('M j') }}">
                        Delete
                    </button>
                </div>
            </div>
            @endforeach

        </div>
    </div>
    @empty
    <p class="text-muted text-center">No session history available.</p>
    @endforelse
</div>

<!-- DELETE MODAL -->
<div class="modal fade" id="deleteSessionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-warning-box p-3">

            <div class="p-3">
                <h6 class="fw-bold">🔴 WARNING: Confirm Session Deletion</h6>

                <p class="mt-2">
                    Deleting this session will remove this from your session history
                    and add 1 session to the client's remaining sessions.
                </p>

                <div class="mt-3">
                    <h6 class="fw-bold">Session Details</h6>
                    <div id="modal-client-name"></div>
                    <div id="modal-client-email"></div>
                    <div id="modal-client-date"></div>
                </div>

                <input type="hidden" id="delete-session-id">

                <button class="btn w-100 mt-4 mindway-btn-blue" id="confirmDeleteBtn">
                    Confirm Session Deletion
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    $(document).on("click", ".delete-session-btn", function() {
        $("#modal-client-name").text("Name: " + $(this).data("name"));
        $("#modal-client-email").text("Email: " + $(this).data("email"));
        $("#modal-client-date").text("Date: " + $(this).data("date"));
        $("#delete-session-id").val($(this).data("id"));
    });

    $(document).on("click", "#confirmDeleteBtn", function() {
        let sessionId = $("#delete-session-id").val();
        let btn = $(this);

        btn.prop("disabled", true).text("Deleting...");

        $.ajax({
            url: "/sessions/delete/" + sessionId,
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                btn.text("Deleted");
                setTimeout(() => {
                    $("#deleteSessionModal").modal("hide");
                }, 500);
                btn.prop("disabled", false).text("Confirm Session Deletion");

                $("button[data-id='" + sessionId + "']").closest(".session-row").fadeOut(300, function() {
                    $(this).remove();
                });
            },
            error: function(xhr) {
                alert("Error deleting session. Try again.");
                btn.prop("disabled", false).text("Confirm Session Deletion");
            }
        });
    });
</script>
@endsection