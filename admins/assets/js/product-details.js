// edit product-details


$('#editProductDetails').on('hidden.bs.modal', function () {
    location.reload();
});

$(document).on('click', '.edit-btn', function () {
    let btn = $(this);
    const id = btn.data('id');
    const name = btn.data('name');
    const image = btn.data('image');
    const options = btn.data('options');
    const value = btn.data('value');
    const gender = btn.data('gender');
    const stock = btn.data('stock');
    const ratings = btn.data('ratings');
    const label = btn.data('label');
    const productId = btn.data('product-id');


    $('#editProductDetails input[name="product_name"]').val(name);
    $('#editProductDetails input[name="product_id"]').val(productId);
    $('#editProductDetails input[name="id"]').val(id);
    $('#editProductDetails input[name="previous_image"]').val(image);
    $('#editProductDetails input[name="previous_image_hidden"]').val(image);
    $('#editProductDetails input[name="option"]').val(options);
    $('#editProductDetails input[name="value"]').val(value);
    $('#editProductDetails select[name="gender"]').val(gender);
    $('#editProductDetails input[name="stock"]').val(stock);
    $('#editProductDetails input[name="ratings"]').val(ratings);
    $('#editProductDetails select[name="label"]').val(label);
});