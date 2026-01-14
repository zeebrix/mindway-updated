document.addEventListener('DOMContentLoaded', function () {
    const user_id = document.getElementById('user_id');
    $('#Yajra-dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: `/counsellor/get-session-data?counsellor_id=${user_id.value}`,
            type: "GET"
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name_email', name: 'name_email' },
            { data: 'company_name', name: 'company_name' },
            { data: 'company_email', name: 'company_email' },
            { data: 'counsellor_name', name: 'counsellor_name' },
            { data: 'session_date', name: 'session_date' },
            { data: 'session_type', name: 'session_type' },
            { data: 'max_session', name: 'max_session' },
        ],
    });
});