// delete
$(document).on('click', '.delete-btn', function () {
    const name = $(this).data('name');
    const id   = $(this).data('id');

    $('#deleteModal .badge').text(name);
    $('#confirmDelete').attr('href', '?action=delete&id=' + id);
});


//! Products page

// status update
$(document).on('click', '#status-update', function () {
    let btn    = $(this);
    let id     = btn.data('id');
    let status = btn.data('status');

    $.ajax({
        url:  'products',
        type: 'POST',
        data: {
            updateStatus: true,
            id:     id,
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


// add product
$(document).on('submit', '#add_product_form', function (e) {
    e.preventDefault();

    let form = $(this);
    let btn  = $('#add_product_btn');

    $.ajax({
        url:  'products',
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


// edit product — populate modal on edit click
$(document).on('click', '.edit-btn', function () {
    let btn = $(this);

    $('#editProduct input[name="id"]').val(btn.data('id'));
    $('#editProduct input[name="name"]').val(btn.data('name'));
    $('#editProduct select[name="category_id"]').val(btn.data('categoryid'));
    $('#editProduct select[name="status"]').val(btn.data('status'));
    $('#editProduct input[name="price"]').val(btn.data('price'));
    $('#editProduct input[name="old_price"]').val(btn.data('oldprice'));
    $('#editProduct input[name="discount"]').val(btn.data('discount'));
    $('#editProduct input[name="properties"]').val(btn.data('properties'));
    $('#editProduct textarea[name="description"]').val(btn.data('description'));
});

// edit product — reload after modal closes
$('#editProduct').on('hidden.bs.modal', function () {
    location.reload();
});

// edit product — submit
$(document).on('submit', '#edit_product_form', function (e) {
    e.preventDefault();

    let form = $(this);
    let btn  = $('#edit_product_btn');

    $.ajax({
        url:  'products',
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

// Price and discount auto-calculations
function setupPriceCalculators(formSelector) {
    const form = $(formSelector);
    const priceInput = form.find('input[name="price"]');
    const oldPriceInput = form.find('input[name="old_price"]');
    const discountInput = form.find('input[name="discount"]');

    function calculateDiscount() {
        let p = parseFloat(priceInput.val());
        let op = parseFloat(oldPriceInput.val());
        if (!isNaN(p) && !isNaN(op) && op > 0) {
            let d = ((op - p) / op) * 100;
            // if discount is negative, user can still see it, but we can just set it
            discountInput.val(Math.round(d));
        }
    }

    function calculatePrice() {
        let d = parseFloat(discountInput.val());
        let op = parseFloat(oldPriceInput.val());
        if (!isNaN(d) && !isNaN(op) && op > 0) {
            let p = op - (op * (d / 100));
            priceInput.val(Math.round(p));
        }
    }

    priceInput.on('input', calculateDiscount);
    
    discountInput.on('input', calculatePrice);
    
    oldPriceInput.on('input', function() {
        // If price is already filled, update discount. Otherwise if discount is filled, update price.
        if (priceInput.val() !== "") {
            calculateDiscount();
        } else if (discountInput.val() !== "") {
            calculatePrice();
        }
    });
}

$(document).ready(function() {
    setupPriceCalculators('#add_product_form');
    setupPriceCalculators('#edit_product_form');
});
