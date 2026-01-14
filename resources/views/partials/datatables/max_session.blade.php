<div class="max-session-wrapper">
    <h6 class="max-session-count">{{ $max_session }}</h6>
    @if($requested)
        <img src="{{ asset('images/icons/timer.png') }}"
             class="requested-session-btn max-session-icon"
             data-bs-toggle="modal"
             data-bs-target="#requestedModal"
             data-requestedId="{{ $customer->pendingRequest->id }}"
             data-customer_name="{{ $customer->name }}"
             title="Already Requested">
    @else
        <img src="{{ asset('images/icons/pluscircle.png') }}"
             class="request-session-btn max-session-icon"
             data-bs-toggle="modal"
             data-bs-target="#requestSessionModal"
             data-id="{{ $customer->id }}"
             data-name="{{ $customer->company_name }}"
             data-program_id="{{ $customer->program_id }}"
             data-app_customer_id="{{ $customer->app_customer_id }}"
             data-customer_name="{{ $customer->name }}"
             title="Request for Extra Sessions">
    @endif
</div>
