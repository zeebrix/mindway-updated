// Modal handler for dynamic Add Session buttons
function handleAddSessionButtons(buttons) {
    const modal = document.getElementById('addSessionModal');
    const customerIdInput = modal.querySelector('input[name="customerId"]');
    const programIdInput = modal.querySelector('input[name="programId"]');
    const modalTitle = modal.querySelector('.modal-title');
    const submitButton = modal.querySelector('button[type="submit"]');

    buttons.forEach(button => {
        button.addEventListener("click", () => {
            const customerId = button.dataset.id;
            const programId = button.dataset.program_id;
            const customerName = button.dataset.customer_name;

            customerIdInput.value = customerId;
            programIdInput.value = programId;

            modalTitle.textContent = `Add Counselling Session for ${customerName}`;
            submitButton.textContent = `ADD SESSION FOR ${customerName}`;
        });
    });
}

// MutationObserver to detect dynamically added elements
const observer = new MutationObserver(mutations => {
    mutations.forEach(mutation => {
        if (mutation.type === "childList" && mutation.addedNodes.length > 0) {
            mutation.addedNodes.forEach(node => {
                if (node.nodeType === 1 && node.classList.contains("add-session-btn")) {
                    handleAddSessionButtons([node]);
                }

                const buttons = node.querySelectorAll?.(".add-session-btn");
                if (buttons?.length) handleAddSessionButtons(buttons);
            });
        }
    });
});

observer.observe(document.body, { childList: true, subtree: true });

// Search Filter
$(document).ready(function () {
    $('#requestSessionModal form').on('submit', function () {
        $('#requestSessionLoader').fadeIn();
    });

    $('#searchInput').on('input', function () {
        const searchText = $(this).val().toLowerCase();
        $('#customersTable .customer-row').each(function () {
            $(this).toggle($(this).text().toLowerCase().includes(searchText));
        });
    });
});

// Toggle Additional Reasons
function toggleAdditionalReasons() {
    const wrapper = document.getElementById('additionalReasons');
    const workRelated = document.getElementById('work_related');
    const otherInput = document.getElementById('other_reason');

    if (workRelated.checked) {
        wrapper.style.display = "block";
    } else {
        wrapper.style.display = "none";
        wrapper.querySelectorAll("input[type=checkbox]").forEach(chk => chk.checked = false);
        otherInput.style.display = "none";
        otherInput.value = "";
    }
}

// Toggle Other Text Input
function toggleOtherInput() {
    const checkbox = document.getElementById('other');
    const input = document.getElementById('other_reason');

    input.style.display = checkbox.checked ? "block" : "none";
    if (!checkbox.checked) input.value = "";
}
