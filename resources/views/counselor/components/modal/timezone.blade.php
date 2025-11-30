<div class="modal fade" id="timezoneModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3">

            <div class="modal-header">
                <h5 class="modal-title fw-bold">Select Time Zone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">

                <!-- Search -->
                <div class="timezone-search p-2 border-bottom">
                    <input
                        type="text"
                        id="timezone-search"
                        class="form-control"
                        placeholder="Search timezones..."
                    >
                </div>

                <!-- List -->
                <div class="timezone-list" id="timezone-list" style="max-height: 250px; overflow-y:auto;">
                    <!-- Timezone items dynamically injected by JS -->
                </div>

            </div>
        </div>
    </div>
</div>
