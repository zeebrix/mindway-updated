document.addEventListener('DOMContentLoaded', function () {
    const uploadTrigger = document.getElementById('uploadLogoTrigger');
    const logoInput = document.getElementById('logoId');
    const previewImage = document.getElementById('previewImage');

    uploadTrigger.addEventListener('click', () => logoInput.click());

    logoInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => previewImage.src = e.target.result;
            reader.readAsDataURL(file);
        }
    });

    // Department modal logic
    const departmentList = document.getElementById('departmentList');
    const departmentsInput = document.getElementById('departments');
    const departmentNameInput = document.getElementById('departmentNameInput');
    const addDepartmentButton = document.getElementById('addDepartmentButton');
    let departments = [];

    function updateDepartmentList() {
        departmentList.innerHTML = '';
        departments.forEach((d, i) => {
            const div = document.createElement('div');
            div.className = 'department-item';
            div.innerHTML = `<span>${d}</span><button type="button" data-index="${i}">Remove</button>`;
            departmentList.appendChild(div);
        });
        departmentsInput.value = JSON.stringify(departments);
    }

    departmentList.addEventListener('click', e => {
        if (e.target.tagName === 'BUTTON') {
            departments.splice(e.target.dataset.index, 1);
            updateDepartmentList();
        }
    });

    addDepartmentButton.addEventListener('click', () => {
        const name = departmentNameInput.value.trim();
        if (name) {
            departments.push(name);
            updateDepartmentList();
            departmentNameInput.value = '';
            const modal = bootstrap.Modal.getInstance(document.getElementById('add-department'));
            modal.hide();
        }
    });

    // Make fields required for Active Program
    const type = "{{ $type }}";
    if (type == 1) {
        ['annual_feeId','cost_per_sessionId','renewal_dateId'].forEach(id => {
            document.getElementById(id)?.setAttribute('required', 'required');
        });
        document.getElementById('active-program').classList.remove('d-none');
        document.getElementById('active-program').classList.add('d-block');
    }

    // Renewal date formatting
    const renewalInput = document.getElementById('renewal_dateId');
    renewalInput?.addEventListener('input', e => {
        let val = e.target.value.replace(/[^0-9]/g,'');
        if(val.length>2) val = val.slice(0,2)+'/'+val.slice(2);
        e.target.value = val.slice(0,5);
    });

     const renewalDateInput = document.getElementById('renewal_date');
    if(renewalDateInput) {
        renewalDateInput.addEventListener('input', function(e){
            let value = e.target.value.replace(/[^0-9]/g,'');
            if(value.length>2) value = value.slice(0,2)+'/'+value.slice(2);
            e.target.value = value.slice(0,5);
        });
    }

    // Upload logo preview
    const logoTrigger = document.getElementById('uploadLogoTrigger');
    if(logoTrigger){
        logoTrigger.addEventListener('click',()=>document.getElementById('logoId').click());
    }
    const logoInput = document.getElementById('logoId');
    if(logoInput){
        logoInput.addEventListener('change', function(event){
            const file = event.target.files[0];
            if(file){
                const reader = new FileReader();
                reader.onload = function(e){
                    document.getElementById('previewImage').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Departments
    let departments1 = window.programDepartments ?? [];
    const departmentList1 = document.getElementById('departmentList');
    const departmentsInput1 = document.getElementById('departments');
    const addBtn = document.getElementById('addDepartmentButton');
    const departmentInput = document.getElementById('departmentNameInput');

    function renderDepartments(){
        departmentList1.innerHTML = '';
        departments1.forEach((d,i)=>{
            const div = document.createElement('div');
            div.classList.add('department-item');
            div.innerHTML = `<span>${d}</span><button type="button" data-index="${i}">Remove</button>`;
            div.querySelector('button').addEventListener('click', ()=>{departments1.splice(i,1); renderDepartments();});
            departmentList1.appendChild(div);
        });
        departmentsInput1.value = JSON.stringify(departments1);
    }

    if(addBtn){
        addBtn.addEventListener('click', ()=>{
            const val = departmentInput1.value.trim();
            if(val){departments1.push(val); renderDepartments(); departmentInput1.value=''; $('#add-department').modal('hide');}
        });
    }

    renderDepartments();
});
