<!-- Loader -->
<div id="requestSessionLoader" class="request-session-loader">
    <div class="loader-center">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<div class="modal fade" id="requestSessionModal" tabindex="-1" aria-labelledby="requestSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="requestSessionModalLabel">Request Additional Sessions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <h6>This request will be sent to the manager of the client's organisation.</h6>
                <h6>Individual employee details will not be disclosed.</h6>
                <h6><strong>Client Name:</strong> <span id="clientNameValue"></span></h6>

                <form action="#" method="POST">
                    @csrf

                    <input type="hidden" name="counselor_id" value="{{ $user_id ?? '' }}">
                    <input type="hidden" name="customerId" id="requestCustomerId">
                    <input type="hidden" name="appCustomerId" id="appCustomerId">
                    <input type="hidden" name="programId" id="programIdv">

                    <h3 class="mt-3">Reason</h3>

                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="request_work_related">
                        <label class="form-check-label">Work Related</label>
                    </div>

                    <div id="requestAdditionalReasons" class="additional-reasons">
                        @php
                            $reasons = [
                                'work_stress' => 'Work Stress',
                                'workplace_conflicts' => 'Workplace Conflicts',
                                'harassment_bullying' => 'Harassment/Bullying',
                                'performance_issues' => 'Performance Issues',
                                'organisational_change' => 'Organisational Change',
                                'burnout' => 'Burnout',
                            ];
                        @endphp

                        @foreach ($reasons as $id => $label)
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="request_{{ $id }}" value="{{ $label }}">
                                <label class="form-check-label">{{ $label }}</label>
                            </div>
                        @endforeach

                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="request_other">
                            <label class="form-check-label">Other</label>
                        </div>

                        <input type="text" id="request_other_reason" name="other_reason"
                               class="form-control mt-2" placeholder="Please specify">
                    </div>

                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" value="Personal Related">
                        <label class="form-check-label">Personal Related</label>
                    </div>

                    <hr>

                    <h6><strong>Recommended Request:</strong> How many additional sessions?</h6>

                    <div class="session-count">
                        @for ($i = 1; $i <= 5; $i++)
                            <div class="btn-group">
                                <input type="radio" class="btn-check" name="request_session_count"
                                       id="session-count-{{ $i }}" value="{{ $i }}" {{ $i === 1 ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary rounded-pill px-4"
                                       for="session-count-{{ $i }}">{{ $i }}</label>
                            </div>
                        @endfor
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-4">
                        Send Request
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
