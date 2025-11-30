document.addEventListener('DOMContentLoaded', function () {

    // ---- Cancel Session with SweetAlert ----
    document.body.addEventListener('click', function(event) {
        const cancelButton = event.target.closest('.js-cancel-session');
        if (!cancelButton) return;

        event.preventDefault();

        const url = cancelButton.dataset.url;
        const userName = cancelButton.dataset.userName;
        const sessionDate = cancelButton.dataset.sessionDate;

        Swal.fire({
            title: 'Confirm Cancellation',
            text: `Are you sure you want to cancel this session with ${userName} on ${sessionDate}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, cancel it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });

    // ---- Work Related Additional Reasons ----
    window.toggleAdditionalReasons = function () {
        const workRelated = document.getElementById('work_related');
        const container = document.getElementById('additionalReasons');
        const otherInput = document.getElementById('other_reason');

        if (workRelated.checked) {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
            container.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
            otherInput.style.display = 'none';
            otherInput.value = '';
        }
    };

    // ---- Other Reason Input ----
    window.toggleOtherInput = function () {
        const otherCheckbox = document.getElementById('other');
        const otherInput = document.getElementById('other_reason');

        otherInput.style.display = otherCheckbox.checked ? 'block' : 'none';
        if (!otherCheckbox.checked) otherInput.value = '';
    };

    // ---- Fill Modal Data ----
    $('#addSessionModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);

        $('#customerId').val(button.data('id'));
        $('#slotId').val(button.data('slot_id'));
        $('#programId').val(button.data('program_id'));
        $('#couselorId').val(button.data('counselor_id'));
    });
});
