$(document).ready(function() {
    // Initialize Bootstrap Modal
    const requestedModalElement = document.getElementById('requestedModal');
    if (!requestedModalElement) {
        console.error('Modal element #requestedModal not found.');
        return;
    }
    const requestedModal = new bootstrap.Modal(requestedModalElement);

    // Show loader on form submission inside the modal
    $('#requestedModal form').on('submit', function() {
        $('#requestSessionLoader').fadeIn();
    });

    // Handle click on 'Review Request' button
    $(document).on('click', '.review-btn', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const status = $(this).data('status'); // Get status from data attribute

        $.get(url)
            .done(function(data) {
                if (data.success) {
                    populateModal(data, status);
                    requestedModal.show();
                } else {
                    alert(data.message || 'Could not retrieve request details.');
                }
            })
            .fail(function() {
                alert('An error occurred while fetching request details.');
            });
    });

    /**
     * Populates the modal with data from the AJAX response.
     * @param {object} data - The response data.
     * @param {string} status - The current tab status ('pending', 'accepted', 'denied').
     */
    function populateModal(data, status) {
        // --- Populate common fields ---
        $('#requestedNameValue').text(data.client_name || 'N/A');
        $('#requestedEmailValue').text(data.client_email || 'N/A');
        $('#requestedIDValue').text(data.client_id || 'N/A');
        $('#CounsellorNameValue').text(data.counselor_name || 'N/A');
        $('#reasonsValue').text(data.reasons || 'N/A');
        $('#clientRequestValue').text(data.request_id || 'N/A');
        $('#addDaysValue, #addDaysValue1').text(data.requested_days || 'N/A');

        // --- Set hidden input values for forms ---
        $('#requestedId').val(data.request_id);
        $('#requestedIdDeny').val(data.request_id);

        // --- Handle date fields based on status ---
        $('#approvedDate, #deniedDate, #requestedDate').hide(); // Hide all date fields initially

        if (status === 'pending') {
            $('#requestedDate').show();
            $('#requestedDateValue').text(data.requested_date || 'N/A');
        } else if (status === 'accepted') {
            $('#approvedDate').show();
            $('#approvedDateValue').text(data.approved_date || 'N/A');
        } else {
            $('#deniedDate').show();
            $('#deniedDateValue').text(data.denied_date || 'N/A');
        }

        // --- Set the requested days radio button for the 'pending' form ---
        if (data.requested_days) {
            const days = parseInt(data.requested_days, 10);
            if (days >= 1 && days <= 5) {
                $(`#session-count-${days}`).prop('checked', true);
            }
        }
    }
});
