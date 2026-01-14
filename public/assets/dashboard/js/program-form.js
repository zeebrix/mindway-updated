document.addEventListener('DOMContentLoaded', function () {

    /* ---------------------------------------------------------
     *  LOGO UPLOAD PREVIEW
     * --------------------------------------------------------- */
    const logoTrigger = document.getElementById('uploadLogoTrigger');
    const logoInput = document.getElementById('logoId');
    const previewImage = document.getElementById('previewImage');
    const user_id = document.getElementById('user_id');

    if (logoTrigger && logoInput) {
        logoTrigger.addEventListener('click', () => logoInput.click());

        logoInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => previewImage.src = e.target.result;
                reader.readAsDataURL(file);
            }
        });
    }


    /* ---------------------------------------------------------
     *  ACTIVE PROGRAM FIELD REQUIREMENTS
     * --------------------------------------------------------- */
    const type = "{{ $type }}";
    if (type == 1) {
        const requiredFields = ['annual_feeId', 'cost_per_sessionId', 'renewal_dateId'];
        requiredFields.forEach(id => {
            document.getElementById(id)?.setAttribute('required', 'required');
        });

        const activeSection = document.getElementById('active-program');
        if (activeSection) {
            activeSection.classList.remove('d-none');
            activeSection.classList.add('d-block');
        }
    }


    /* ---------------------------------------------------------
     *  RENEWAL DATE FORMATTING (DD/MM)
     * --------------------------------------------------------- */
    function bindRenewalFormatter(input) {
        if (!input) return;

        input.addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 2) v = v.slice(0, 2) + '/' + v.slice(2);
            e.target.value = v.slice(0, 5);
        });
    }

    bindRenewalFormatter(document.getElementById('renewal_dateId'));
    bindRenewalFormatter(document.getElementById('renewal_date'));


    /* ---------------------------------------------------------
     *  DEPARTMENT HANDLER
     * --------------------------------------------------------- */
    const el = document.getElementById('departmentsData');
    let departments = [];

    if (el && el.value) {
        try {
            departments = JSON.parse(el.value);
        } catch (e) {
            departments = [];
        }
    }
    const departmentList = document.getElementById('departmentList');
    const departmentsInput = document.getElementById('departments');
    const addDepartmentButton = document.getElementById('addDepartmentButton');
    const departmentNameInput = document.getElementById('departmentNameInput');

    function renderDepartments() {
        if (!departmentList) return;

        departmentList.innerHTML = '';

        departments.forEach((d, i) => {
            const name = (typeof d === 'object' && d !== null) ? d.name : d;
            const div = document.createElement('div');
            div.classList.add('department-item');
            div.innerHTML = `
                <span>${name}</span>
                <button type="button" class="remove-dept" data-index="${i}">Remove</button>
            `;
            departmentList.appendChild(div);
        });

        if (departmentsInput) {
            departmentsInput.value = JSON.stringify(departments);
        }
    }

    if (departmentList) {
        departmentList.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-dept')) {
                const index = e.target.dataset.index;
                departments.splice(index, 1);
                renderDepartments();
            }
        });
    }

    if (addDepartmentButton) {
        addDepartmentButton.addEventListener('click', () => {
            const value = departmentNameInput.value.trim();
            if (value) {
                departments.push(value);
                departmentNameInput.value = '';
                renderDepartments();

                // hide modal
                const modalEl = document.getElementById('add-department');
                if (modalEl) bootstrap.Modal.getInstance(modalEl)?.hide();
            }
        });
    }

    renderDepartments();

    const previewBtn = document.getElementById('previewDataBtn');
    const uploadBtn = document.getElementById('uploadDataBtn');
    const fileInput = document.getElementById('uploadFile');
    const form = document.getElementById('dataFormBulk');
    const previewTableBody = document.getElementById('previewTableBody');

    previewBtn.addEventListener('click', function () {
        const file = fileInput.files[0];

        if (!file) {
            alert("Please upload a file.");
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            const data = new Uint8Array(event.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const sheet = workbook.SheetNames[0];
            const sheetData = XLSX.utils.sheet_to_json(workbook.Sheets[sheet], { header: 1 });

            populatePreviewTable(sheetData);
            uploadBtn.hidden = false;
        };

        reader.readAsArrayBuffer(file);
    });

    function populatePreviewTable(data) {
        previewTableBody.innerHTML = "";

        data.slice(1).forEach((row, index) => {
            if (row.length > 1) {
                const tr = document.createElement('tr');

                tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${row[0]}</td>
                    <td>${row[1]}</td>
                    <td><i class="ti ti-trash delete-row-btn"></i></td>
                `;

                tr.querySelector('.delete-row-btn').addEventListener('click', () => {
                    tr.remove();
                });

                previewTableBody.appendChild(tr);
            }
        });
    }

    form.addEventListener('submit', function () {
        const rows = document.querySelectorAll('#previewTableBody tr');
        const finalData = [];

        rows.forEach(row => {
            const cells = row.children;

            finalData.push({
                id: cells[0].textContent.trim(),
                name: cells[1].textContent.trim(),
                email: cells[2].textContent.trim()
            });
        });

        document.getElementById('finalDataInput').value = JSON.stringify(finalData);
    });

    $('#Yajra-dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: `/admin/programs/${user_id.value}/get-customer-data`,
            type: "GET"
        },
        columns: [
            { data: 'name_email', name: 'name_email' },
            { data: 'level', name: 'level' },
            { data: 'max_session', name: 'max_session' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
    });


});
document.querySelectorAll('.js-program-action').forEach(btn => {

    btn.addEventListener('click', () => {
        const action = btn.dataset.action;
        if (!confirm('Are you sure you want to delete permanently?')) {
            return;
        }

        const form = document.getElementById('programActionForm');
        document.getElementById('action').value = action;

        form.submit();
    });

});
(() => {
    'use strict';

    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            let isValid = true;

            // Custom validation for Employees Visible radio
            const radioGroup = form.querySelectorAll('input[name="allow_employees"]');
            const feedback = form.querySelector('#allow-employees-group + .invalid-feedback');

            // Check if any radio is checked
            const checked = Array.from(radioGroup).some(r => r.checked);
            if (!checked) {
                isValid = false;
                feedback.style.display = 'block'; // show error
            } else {
                feedback.style.display = 'none'; // hide error
            }

            if (!form.checkValidity() || !isValid) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        }, false);
    });
})();