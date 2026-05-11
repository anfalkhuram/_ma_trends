<?php
require_once("inc/config.php");

if (!isLoggedIn()) {
    exit();
}

$userId = $_SESSION['user']['id'] ?? $_SESSION['admin']['id'];
$sqlCart = "SELECT c.*, p.name as product_name, pd.image, pd.options, pd.value 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            JOIN product_details pd ON pd.product_id = p.id 
            WHERE c.user_id = $userId";
$resCart = mysqli_query($conn, $sqlCart);
$cartItemsCount = mysqli_num_rows($resCart);

$subtotal = 0;
?>
<div class="col-lg-8">
    <div class="ma-card p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="fw-bold">Products</div>
            <div class="small ma-muted"><?php echo $cartItemsCount; ?> items</div>
        </div>

        <?php
        if ($cartItemsCount > 0):
            while ($item = mysqli_fetch_assoc($resCart)):
                $subtotal += $item['sub_total'];
        ?>
            <!-- Item -->
            <div class="ma-card p-3 mb-3 js-cart-item" data-price="<?php echo $item['price']; ?>" data-id="<?php echo $item['id']; ?>">
                <div class="row g-3 align-items-center">
                    <div class="col-4 col-md-3">
                        <img src="./admins/assets/images/products/<?php echo $item['image']; ?>" class="w-100 ma-rounded" alt="<?php echo $item['product_name']; ?>" />
                    </div>
                    <div class="col-8 col-md-5">
                        <div class="fw-semibold"><?php echo $item['product_name']; ?></div>
                        <div class="ma-muted small text-capitalize"><?php echo $item['options']; ?> • <?php echo $item['value']; ?></div>
                        <div class="d-flex gap-2 mt-2">
                            <a class="text-decoration-none ma-muted small" href="products?id=<?php echo $item['product_id']; ?>">View details</a>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="js-qty-wrap d-flex align-items-center gap-2">
                            <button class="btn btn-ma-outline btn-sm js-qty-minus" type="button">−</button>
                            <input class="form-control form-control-sm js-qty text-center" value="<?php echo $item['quantity']; ?>" readonly />
                            <button class="btn btn-ma-outline btn-sm js-qty-plus" type="button">+</button>
                        </div>
                    </div>
                    <div class="col-md-2 text-md-end">
                        <div class="ma-price js-line-total">Rs. <?php echo number_format($item['sub_total'], 2); ?></div>
                        <a href="#" class="text-decoration-none small ma-muted js-remove-cart" data-id="<?php echo $item['id']; ?>">Remove</a>
                    </div>
                </div>
            </div>
        <?php 
            endwhile;
        else:
        ?>
            <div class="text-center py-5">
                <div class="ma-muted mb-3">Your cart is empty</div>
                <a href="shop" class="btn btn-ma">Go to Shop</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="col-lg-4">
    <div class="ma-card p-3 p-md-4">
        <div class="fw-bold mb-3">Order summary</div>
        <div class="d-flex justify-content-between mb-2">
            <div class="ma-muted">Subtotal</div>
            <div class="js-cart-subtotal">Rs. <?php echo number_format($subtotal, 2); ?></div>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <div class="ma-muted">Delivery</div>
            <div class="js-cart-shipping">Free</div>
        </div>
        <hr class="border ma-border my-3" />
        <div class="d-flex justify-content-between align-items-center">
            <div class="fw-bold">Total</div>
            <div class="h5 mb-0 ma-price js-cart-total">Rs. <?php echo number_format($subtotal, 2); ?></div>
        </div>
        <a class="btn btn-ma w-100 mt-3" href="checkout">Proceed to checkout</a>
    </div>
</div>
