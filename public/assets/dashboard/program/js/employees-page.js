// Assuming jQuery and Bootstrap's modal functionality are available globally,
// as suggested by the use of `$('#adminLevelModal').modal('show');`

// 1. Logic for showing the Admin Level Modal
document.querySelectorAll('#changeLevel').forEach(td => {
    td.addEventListener('click', function () {
        // Get user ID and current level
        const memberId = this.getAttribute('data-id');
        var currentLevel = this.getAttribute("data-level");

        // Set the modal values
        document.getElementById('memberIdInput').value = memberId;
        if (currentLevel === 'member') {
            document.getElementById('levelMember').checked = true;
        } else if (currentLevel === 'admin') {
            document.getElementById('levelAdmin').checked = true;
        }
        // Show the modal using the modern Bootstrap 5 API
        const adminLevelModal = new bootstrap.Modal(document.getElementById('adminLevelModal'));
        adminLevelModal.show();
    });
});

// 2. Logic for submitting the Admin Level change
document.getElementById("submitAdminLevel").addEventListener("click", function () {
    var memberId = document.getElementById("memberIdInput").value;
    const newLevel = document.querySelector('input[name="level"]:checked').value;

    if (!memberId) {
        alert('Please select a member.');
        return;
    }

    // Get CSRF token from the meta tag (best practice for external JS)
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Send the API request to update admin level
    fetch('/update-customer-level', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken // Use the token from the meta tag
        },
        body: JSON.stringify({
            member_id: memberId,
            admin_level: newLevel
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Admin level updated successfully!');
                const td = document.querySelector(`#changeLevel[data-id="${memberId}"]`);
                const span = td.querySelector('span');
                span.textContent = newLevel;

                // Update classes for styling
                if (newLevel === 'member') {
                    span.classList.add('member-style');
                    span.classList.remove('admin-style');
                } else {
                    span.classList.add('admin-style');
                    span.classList.remove('member-style');
                }
                td.setAttribute('data-level', newLevel);

                // Hide the modal using the modern Bootstrap 5 API
                const adminLevelModal = bootstrap.Modal.getInstance(document.getElementById('adminLevelModal'));
                adminLevelModal.hide();
            } else {
                alert('Failed to update admin level.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the admin level.');
        });
});

// 3. Logic for the search functionality
document.getElementById('searchInput').addEventListener('keyup', function () {
    const filter = this.value.toLowerCase();
    const table = document.getElementById('employeeTable');
    const rows = table.querySelectorAll('tbody tr');

    rows.forEach(row => {
        // Assuming the structure is:
        // td[1] (name/email block)
        // td[2] (level)
        // td[3] (remove button)

        // Get name and email from the first td
        const firstTdContent = row.querySelector('td:nth-child(1)').innerText;
        const [name, email] = firstTdContent.split('\n').map(s => s.trim().toLowerCase());

        if (name.includes(filter) || email.includes(filter)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// 4. Logic for the Remove button (SweetAlert2)
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.remove-btn').forEach(button => {
        button.addEventListener('click', function () {
            const employeeName = this.dataset.name;
            const employeeEmail = this.dataset.email;
            const customerId = this.dataset.id;

            // Check if Swal is available (SweetAlert2)
            if (typeof Swal === 'undefined') {
                console.error('SweetAlert2 is not loaded. Cannot show confirmation dialog.');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to remove ${employeeName} (${employeeEmail})?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // The original code uses a dynamic form submission to a Blade route.
                    // Since the route name `remove-customer` and the CSRF token are server-side
                    // variables, we need to ensure they are available.
                    // The CSRF token will be retrieved from the meta tag in the HTML.
                    // The route URL needs to be passed via a data attribute or a small script block.

                    // Assuming the route URL is available in a global variable or data attribute
                    const removeCustomerRoute = document.body.dataset.removeCustomerRoute;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    if (!removeCustomerRoute) {
                        console.error('Remove customer route is not defined.');
                        return;
                    }

                    // Create a form dynamically and submit it
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = removeCustomerRoute;

                    // Add CSRF token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);

                    // Add customerId
                    const idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'customerId';
                    idInput.value = customerId;
                    form.appendChild(idInput);

                    // Add email
                    const emailInput = document.createElement('input');
                    emailInput.type = 'hidden';
                    emailInput.name = 'email';
                    emailInput.value = employeeEmail;
                    form.appendChild(emailInput);

                    // Append and submit form
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
});