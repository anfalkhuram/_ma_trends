// delete
$(document).on('click', '.delete-btn', function () {
    const name = $(this).data('name');
    const id = $(this).data('id');

    $('#deleteModal .badge').text(name);
    $('#confirmDelete').attr('href', '?action=delete&id=' + id);


});


//! Categories page
// status update
$(document).on('click', '#status-update', function () {
    let btn = $(this);
    let id = btn.data('id');
    let status = btn.data('status');

    $.ajax({
        url: 'categories',
        type: 'POST',
        data: {
            updateStatus: true,
            id: id,
            status: status
        },

        beforeSend: function () {
            btn.prop('disabled', true);
        },

        complete: function () {
            if (status == 1) {
                btn.removeClass('badge-trending')
                    .addClass('badge-ma')
                    .text('hidden')
                    .data('status', 0);
            } else {
                btn.removeClass('badge-ma')
                    .addClass('badge-trending')
                    .text('active')
                    .data('status', 1);
            }

            btn.prop('disabled', false);

        }


    });
});

// add category
$(document).on('submit', '#add_category_form', function (e) {
    e.preventDefault();

    let form = $(this);
    let btn = $('#add_category_btn');



    $.ajax({
        url: 'categories',
        type: 'POST',
        data: form.serialize(),
        beforeSend: function () {
            btn.prop('disabled', true).text('Saving...');
        },
        success: function () {
            setTimeout(function () {
                btn.prop('disabled', false).text('Saved');
                location.reload(function () {
                    form[0].reset();
                });

            }, 1000);

        },
        error: function () {
            setTimeout(function () {
                btn.text('Failed');
            }, 1000);
            setTimeout(function () {
                btn.prop('disabled', false).text('Retry');
            }, 1000);

        }


    });

});


// edit category
$('#editCategory').on('hidden.bs.modal', function () {
    location.reload();
});

$(document).on('click', '.edit-btn', function () {
    let btn = $(this);
    const id = btn.data('id');
    const name = btn.data('name');
    const slug = btn.data('slug');
    const status = btn.data('status');


    $('#editCategory input[name="name"]').val(name);
    $('#editCategory input[name="slug"]').val(slug);
    $('#editCategory select[name="status"]').val(status);
    $('#editCategory input[name="id"]').val(id);
});


$(document).on('submit', '#edit_category_form', function (e) {
    e.preventDefault();

    let form = $(this);
    let btn = $('#edit_category_btn');

    $.ajax({
        url: 'categories',
        type: 'POST',
        data: form.serialize(),
        beforeSend: function () {
            btn.prop('disabled', true).text('Saving...');
        },
        success: function () {
            setTimeout(function () {
                btn.prop('disabled', false).text('Saved');
                location.reload(function () {
                    form[0].reset();
                });

            }, 1000);

        },
        error: function () {
            setTimeout(function () {
                btn.text('Failed');
            }, 1000);
            setTimeout(function () {
                btn.prop('disabled', false).text('Retry');
            }, 1000);

        }

    });

});

// form jquery
$('input[name="name"]').on('input', function () {
    let name = $(this).val();

    let slugPart = name
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');

    $('input[name="slug"]').val('/category-' + slugPart);
});









