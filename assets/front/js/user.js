$('#confirm-delete').on('show.bs.modal', function(e) {
    $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
});

$('#confirm-delete .btn-ok').on('click', function(e) {
    if ($('#confirm-delete .btn-ok').hasClass("order-btn")) {
        if (admin_loader == 1) {
            $('.submit-loader').show();
        }
    }
    $.ajax({
        type: "GET",
        url: $(this).attr('href'),
        success: function(data) {
            $('#confirm-delete').modal('toggle');
            $('#geniustable').DataTable().ajax.reload();
            $('.alert-danger').hide();
            $('.alert-success').show();
            $('.alert-success p').html(data);

            if ($('#confirm-delete .btn-ok').hasClass("order-btn")) {
                if (admin_loader == 1) {
                    $('.submit-loader').hide();
                }
            }

        }
    });
    return false;
});

//**************************** USER FORM SUBMIT SECTION ENDS ****************************************
$(document).on('change', '.data-droplinks', function(e) {
    $('#confirm-delete').modal('show');
    $('#confirm-delete').find('.btn-ok').attr('href', $(this).val());
    $('#geniustable').DataTable().ajax.reload();
});


$('#example').DataTable({
    "paging": true,
    "ordering": false,
    "info": true
});