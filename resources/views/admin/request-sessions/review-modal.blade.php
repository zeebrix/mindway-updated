<!-- Loader -->
<div id="requestSessionLoader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:1060;">
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">
        <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;"><span class="visually-hidden">Loading...</span></div>
    </div>
</div>

<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>Request ID #R<span id="modalRequestId"></span></strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6><strong>Client Name: </strong><span id="modalClientName"></span></h6>
                <h6><strong>Client Email: </strong><span id="modalClientEmail"></span></h6>
                <h6><strong>Client ID: </strong><span id="modalClientId"></span></h6>
                <h6><strong>Counselor: </strong><span id="modalCounselorName"></span></h6>
                <h6><strong>Reason(s): </strong><span id="modalReasons"></span></h6>
                <h6><strong>Date Requested: </strong><span id="modalRequestedDate"></span></h6>
                <hr>
                <h6><strong>Recommended Request: </strong> How many additional sessions?</h6>
                
                {{-- This form now submits to the dedicated 'approve' route --}}
                <form id="approveForm" action="" method="POST">
                    @csrf
                    <div class="d-flex flex-wrap justify-content-start mt-3" style="gap: 10px;">
                        @for ($i = 1; $i <= 5; $i++)
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="request_session_count" id="session-count-{{$i}}" value="{{$i}}" autocomplete="off" {{ $i == 1 ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary rounded-pill px-4" for="session-count-{{$i}}">{{$i}}</label>
                        </div>
                        @endfor
                    </div>
                </form>
            </div>
            <div class="modal-footer modal-footer-actions">
                {{-- The Deny button is now a separate form --}}
                <form id="denyForm" action="" method="POST" class="w-100">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100 mb-2">Deny Sessions</button>
                </form>
                {{-- The Approve button submits the form in the modal body --}}
                <button type="submit" form="approveForm" class="btn btn-primary w-100">Approve Sessions</button>
            </div>
        </div>
    </div>
</div>
