document.addEventListener('DOMContentLoaded', function() {
    // --- Tab Switching Logic ---
    const tabs = document.querySelectorAll('.settings-tabs .nav-link');
    const tabContents = document.querySelectorAll('.tab-pane');

    tabs.forEach(clickedTab => {
        clickedTab.addEventListener('click', function(e) {
            e.preventDefault();

            // Deactivate all tabs and content
            tabs.forEach(tab => tab.classList.remove('active'));
            tabContents.forEach(content => content.style.display = 'none');

            // Activate the clicked tab and its content
            this.classList.add('active');
            const activeContent = document.querySelector(this.getAttribute('href'));
            if (activeContent) {
                activeContent.style.display = 'block';
            }
        });
    });

    // --- Edit Organization Name ---
    const editNameBtn = document.getElementById('edit-name-btn');
    const companyNameInput = document.getElementById('company_nameId');
    const editNameContainer = document.getElementById('edit-name-container');

    if (editNameBtn) {
        editNameBtn.addEventListener('click', function() {
            companyNameInput.readOnly = false;
            companyNameInput.focus();

            // Replace icon with a save button
            editNameContainer.innerHTML = `
                <button id="save-name-btn" class="btn btn-primary btn-sm btn-save">Save</button>
            `;

            // Add event listener for the new save button
            document.getElementById('save-name-btn').addEventListener('click', saveCompanyName);
        });
    }

    function saveCompanyName() {
        const companyName = companyNameInput.value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/manage-program/save-name', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ company_name: companyName })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    toastr.success("Organization Name Updated Successfully");
                    companyNameInput.readOnly = true;
                    editNameContainer.innerHTML = `
                        <i id="edit-name-btn" class="ti ti-pencil settings-card__action--editable"></i>
                    `;
                    // Re-attach listener to the new edit icon
                    document.getElementById('edit-name-btn').addEventListener('click', /*... re-enable editing ...*/);
                } else {
                    toastr.error(data.message || "Failed to update name.");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error("An error occurred.");
            });
    }

    // --- Logo Upload Logic ---
    const uploadCard = document.getElementById('uploadLogoTrigger');
    const logoInput = document.getElementById('uploadLogoInput');

    if (uploadCard) {
        uploadCard.addEventListener('click', () => logoInput.click());
    }

    if (logoInput) {
        logoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('logo', file);
            // The company name is not needed here as per your original code's endpoint

            const uploadUrl = this.dataset.uploadUrl; // Get URL from data attribute
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(uploadUrl, {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        toastr.success("Logo Updated Successfully. Page will reload.");
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        toastr.error(data.message || "Error uploading logo.");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    toastr.error("An unexpected error occurred during upload.");
                });
        });
    }

    // --- 2FA Toggle Logic ---
    const enable2faCheckbox = document.getElementById('enable_2fa');
    const setup2faDiv = document.getElementById('2fa-setup');

    if (enable2faCheckbox) {
        enable2faCheckbox.addEventListener('change', function() {
            setup2faDiv.style.display = this.checked ? 'block' : 'none';
        });
    }
});
