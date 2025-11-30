toastr.options = { "closeButton": true, "progressBar": true, "positionClass": "toast-top-right" };

$(document).on('click', '.action-delete', function (e) {
    e.preventDefault();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    const deleteButton = $(this);
    const deleteUrl = deleteButton.attr('href');
    const tableId = deleteButton.closest('table').attr('id');

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                dataType: 'json',
                success: function (response) {
                    Swal.fire(
                        'Deleted!',
                        response.message || 'The item has been deleted.',
                        'success'
                    );
                    if (window.DataTableInstances && window.DataTableInstances[tableId]) {
                        window.DataTableInstances[tableId].ajax.reload(null, false);
                    }
                },
                error: function (xhr) {
                    Swal.fire(
                        'Error!',
                        'Something went wrong. Please try again.',
                        'error'
                    );
                }
            });
        }
    });
});
