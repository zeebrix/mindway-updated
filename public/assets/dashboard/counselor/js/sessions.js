(function ($) {

    /* -----------------------------
     |  DataTable Initialization
     |----------------------------- */
    document.addEventListener('DOMContentLoaded', function () {
        const userInput = document.getElementById('user_id');
        if (!userInput) return;

        $('#Yajra-dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: `/counselor/get-client-data?counsellor_id=${userInput.value}`,
                type: "GET"
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name_email', name: 'name_email' },
                { data: 'company_name', name: 'company_name' },
                { data: 'max_session', name: 'max_session' },
                { data: 'action', name: 'action' },
            ],
        });
    });

    /* -----------------------------
     |  Add Session Modal Handler
     |----------------------------- */
    function bindAddSessionButtons(buttons) {
        const modal = document.getElementById('addSessionModal');
        if (!modal) return;

        const customerIdInput = modal.querySelector('[name="customerId"]');
        const programIdInput = modal.querySelector('[name="programId"]');
        const modalTitle = modal.querySelector('.modal-title');
        const submitButton = modal.querySelector('button[type="submit"]');

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                customerIdInput.value = button.dataset.id || '';
                programIdInput.value = button.dataset.program_id || '';

                const name = button.dataset.customer_name || '';
                modalTitle.textContent = `Add Counselling Session for ${name}`;
                submitButton.textContent = `ADD SESSION FOR ${name}`;
            });
        });
    }

    /* -----------------------------
     |  Request Session Modal
     |----------------------------- */
    function bindRequestSessionButtons($buttons) {
        $buttons.each(function () {
            const $btn = $(this);

            $btn.tooltip({ trigger: 'hover', placement: 'top' });

            $btn.off('click').on('click', function () {
                $('#requestCustomerId').val($btn.data('id') || '');
                $('#programIdv').val($btn.data('program_id') || '');
                $('#appCustomerId').val($btn.data('app_customer_id') || '');
                $('#clientNameValue').text($btn.data('customer_name') || '');
            });
        });
    }

    /* -----------------------------
     |  Global Mutation Observer
     |----------------------------- */
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            mutation.addedNodes.forEach(node => {
                if (node.nodeType !== 1) return;

                // Add Session Buttons
                if (node.classList.contains('add-session-btn')) {
                    bindAddSessionButtons([node]);
                }
                bindAddSessionButtons(node.querySelectorAll?.('.add-session-btn') || []);

                // Request Session Buttons
                const $requestBtns = $(node).hasClass('request-session-btn')
                    ? $(node)
                    : $(node).find('.request-session-btn');

                if ($requestBtns.length) {
                    bindRequestSessionButtons($requestBtns);
                }
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    /* -----------------------------
     |  DOM Ready Handlers
     |----------------------------- */
    $(document).ready(function () {

        // Initial bindings
        bindAddSessionButtons(document.querySelectorAll('.add-session-btn'));
        bindRequestSessionButtons($('.request-session-btn'));

        // Loader on submit
        $('#requestSessionModal form').on('submit', function () {
            $('#requestSessionLoader').fadeIn();
        });

        // Search filter
        $('#searchInput').on('input', function () {
            const text = $(this).val().toLowerCase();
            $('#customersTable .customer-row').each(function () {
                $(this).toggle($(this).text().toLowerCase().includes(text));
            });
        });

        // Toggle work-related reasons
        $('#request_work_related').on('change', function () {
            const isChecked = this.checked;
            $('#requestAdditionalReasons').toggle(isChecked);

            if (!isChecked) {
                $('#requestAdditionalReasons input[type="checkbox"]').prop('checked', false);
                $('#request_other_reason').hide().val('');
            }
        });

        // Toggle other input
        $('#request_other').on('change', function () {
            $('#request_other_reason').toggle(this.checked).val(this.checked ? '' : '');
        });
    });

})(jQuery);
