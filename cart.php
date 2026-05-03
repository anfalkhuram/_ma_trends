<?php
session_start();
require_once("inc/config.php");

/* ===============================
   PAGE SEO
================================= */
$pageTitle = "Your Cart | MATrends";
$description = "Review your MATrends cart, update quantity and proceed to checkout.";
$keywords = "cart, checkout, MATrends";
$author = "MATrends";
$robots = "noindex,nofollow";

/* ===============================
   USER ID
================================= */
$user_id = isset($_SESSION['cart_user_id']) ? (int) $_SESSION['cart_user_id'] : 1;
$_SESSION['cart_user_id'] = $user_id;

$orderSuccessMessage = $_SESSION['order_success_message'] ?? '';
unset($_SESSION['order_success_message']);

/* ===============================
   ADD / UPDATE / REMOVE LOGIC
================================= */

// Increase Qty
if (isset($_POST['increase'])) {

    $cart_id = $_POST['cart_id'];

    $sql = "SELECT * FROM cart WHERE id='$cart_id' AND user_id='$user_id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    $qty = $row['qty'] + 1;
    $subtotal = $row['price'] * $qty;

    mysqli_query($conn, "UPDATE cart 
        SET qty='$qty', sub_total='$subtotal' 
        WHERE id='$cart_id'");
}

// Decrease Qty
if (isset($_POST['decrease'])) {

    $cart_id = $_POST['cart_id'];

    $sql = "SELECT * FROM cart WHERE id='$cart_id' AND user_id='$user_id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    $qty = $row['qty'] - 1;

    if ($qty < 1) {
        $qty = 1;
    }

    $subtotal = $row['price'] * $qty;

    mysqli_query($conn, "UPDATE cart 
        SET qty='$qty', sub_total='$subtotal' 
        WHERE id='$cart_id'");
}

// Update Qty
if (isset($_POST['update_qty'])) {

    $cart_id = $_POST['cart_id'];
    $qty = $_POST['qty'];

    if ($qty < 1) {
        $qty = 1;
    }

    $sql = "SELECT * FROM cart WHERE id='$cart_id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    $subtotal = $row['price'] * $qty;

    mysqli_query($conn, "UPDATE cart 
        SET qty='$qty', sub_total='$subtotal' 
        WHERE id='$cart_id'");
}

// Remove Item
if (isset($_POST['remove_item'])) {

    $cart_id = $_POST['cart_id'];

    mysqli_query($conn, "DELETE FROM cart 
        WHERE id='$cart_id' AND user_id='$user_id'");
}

// Clear Cart
if (isset($_POST['clear_cart'])) {

    mysqli_query($conn, "DELETE FROM cart 
        WHERE user_id='$user_id'");
}

/* ===============================
   FETCH CART ITEMS
================================= */

$sql = "SELECT cart.*, 
        products.name,
        products.old_price,
        products.discount,
        product_details.image,
        product_details.stock

        FROM cart

        INNER JOIN products 
        ON cart.product_id = products.id

        LEFT JOIN product_details
        ON cart.product_id = product_details.product_id

        WHERE cart.user_id='$user_id'

        ORDER BY cart.id DESC";

$result = mysqli_query($conn, $sql);

/* ===============================
   TOTALS
================================= */

$grandTotal = 0;
$itemCount = 0;

$temp = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($temp)) {

    $grandTotal += $row['sub_total'];
    $itemCount += $row['qty'];
}

$shipping = 250;

if ($grandTotal == 0) {
    $shipping = 0;
}

$totalPayable = $grandTotal + $shipping;

function price($amount)
{
    return "Rs. " . number_format($amount);
}

require_once("inc/top.php");
?>

<body>

    <?php require_once("inc/navbar.php"); ?>

    <main class="ma-hero pb-5">

        <div class="container">

            <!-- HEADER -->
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">

                <div>
                    <span class="badge badge-ma rounded-pill px-3 py-2 mb-3">
                        Shopping Cart
                    </span>

                    <h1 class="display-6 fw-bold mb-2 text-white">
                        Review your cart and continue to checkout
                    </h1>

                    <p class="ma-muted mb-0">
                        Update quantity, remove items and proceed.
                    </p>
                </div>

                <div class="ma-card px-4 py-3 text-center">
                    <div class="small ma-muted">Items in cart</div>
                    <div class="h3 mb-0 fw-bold">
                        <?php echo $itemCount; ?>
                    </div>
                </div>

            </div>

            <div class="row g-4">

                <?php if ($orderSuccessMessage != '') { ?>
                    <div class="col-12">
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($orderSuccessMessage); ?>
                        </div>
                    </div>
                <?php } ?>

                <!-- LEFT -->
                <div class="col-lg-8">

                    <div class="ma-card p-3 p-md-4">

                        <div class="d-flex justify-content-between align-items-center mb-3">

                            <h2 class="h4 fw-bold mb-0">Cart Items</h2>

                            <?php if ($itemCount > 0) { ?>
                                <form method="POST">
                                    <button class="btn btn-outline-danger btn-sm"
                                        name="clear_cart">
                                        Clear Cart
                                    </button>
                                </form>
                            <?php } ?>

                        </div>

                        <?php if ($itemCount == 0) { ?>

                            <div class="text-center py-5">

                                <div class="h5 fw-semibold mb-2">
                                    Your cart is empty
                                </div>

                                <p class="ma-muted mb-4">
                                    Add products first.
                                </p>

                                <a href="shop" class="btn btn-ma">
                                    Continue Shopping
                                </a>

                            </div>

                        <?php } else { ?>

                            <div class="d-flex flex-column gap-3">

                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                                    <div class="border rounded-4 p-3">

                                        <div class="row g-3 align-items-center">

                                            <!-- IMAGE -->
                                            <div class="col-md-2 col-4">

                                                <img
                                                    src="./admins/assets/images/products/<?php echo $row['image']; ?>"
                                                    class="img-fluid rounded-3 w-100">

                                            </div>

                                            <!-- NAME -->
                                            <div class="col-md-4 col-8">

                                                <h3 class="h6 fw-bold mb-1">
                                                    <?php echo $row['name']; ?>
                                                </h3>

                                                <div class="ma-muted small mb-2">
                                                    Unit Price:
                                                    <?php echo price($row['price']); ?>
                                                </div>

                                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                    Stock:
                                                    <?php echo $row['stock']; ?>
                                                </span>

                                            </div>

                                            <!-- QTY -->
                                            <div class="col-md-3">

                                                <div class="d-flex align-items-center justify-content-md-center gap-2 flex-nowrap">

                                                    <form method="POST" class="m-0 d-flex align-items-center">
                                                        <input type="hidden" name="cart_id"
                                                            value="<?php echo $row['id']; ?>">
                                                        <button class="btn btn-ma-outline btn-sm d-flex align-items-center justify-content-center"
                                                            style="width:40px; height:40px;"
                                                            name="decrease">-</button>
                                                    </form>

                                                    <form method="POST" class="m-0 d-flex align-items-center gap-2">

                                                        <input type="hidden" name="cart_id"
                                                            value="<?php echo $row['id']; ?>">

                                                        <input type="number"
                                                            name="qty"
                                                            min="1"
                                                            value="<?php echo $row['qty']; ?>"
                                                            class="form-control text-center"
                                                            style="width:80px; height:40px;">

                                                        <button class="btn btn-light btn-sm"
                                                            style="height:40px;"
                                                            name="update_qty">
                                                            Update
                                                        </button>

                                                    </form>

                                                    <form method="POST" class="m-0 d-flex align-items-center">
                                                        <input type="hidden" name="cart_id"
                                                            value="<?php echo $row['id']; ?>">
                                                        <button class="btn btn-ma-outline btn-sm d-flex align-items-center justify-content-center"
                                                            style="width:40px; height:40px;"
                                                            name="increase">+</button>
                                                    </form>

                                                </div>

                                            </div>

                                            <!-- PRICE -->
                                            <div class="col-md-3 text-md-end">

                                                <div class="fw-semibold mb-2">
                                                    <?php echo price($row['sub_total']); ?>
                                                </div>

                                                <form method="POST">
                                                    <input type="hidden" name="cart_id"
                                                        value="<?php echo $row['id']; ?>">

                                                    <button class="btn btn-outline-danger btn-sm"
                                                        name="remove_item">
                                                        Remove
                                                    </button>

                                                </form>

                                            </div>

                                        </div>

                                    </div>

                                <?php } ?>

                            </div>

                        <?php } ?>

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="col-lg-4">

                    <div class="ma-card p-4 mb-4">

                        <h2 class="h4 fw-bold mb-3">
                            Order Summary
                        </h2>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="ma-muted">Subtotal</span>
                            <span><?php echo price($grandTotal); ?></span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="ma-muted">Shipping</span>
                            <span><?php echo price($shipping); ?></span>
                        </div>

                        <hr class="border ma-border">

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Total</span>
                            <span class="h5 fw-bold mb-0">
                                <?php echo price($totalPayable); ?>
                            </span>
                        </div>

                        <div class="d-grid gap-2 mt-4">

                            <a href="orders.php" class="btn btn-ma">
                                Proceed Checkout
                            </a>

                            <a href="shop" class="btn btn-ma-outline">
                                Continue Shopping
                            </a>

                        </div>

                    </div>

                    <div class="ma-card p-4">

                        <h3 class="h5 fw-bold mb-3">
                            Why shop with us?
                        </h3>

                        <div class="ma-muted small">
                            Fast shipping, modern accessories, easy cart flow.
                        </div>

                    </div>

                </div>

            </div>

          

        </div>
    </main>

    <?php require_once("inc/footer.php"); ?>
    <?php require_once("inc/bottom.php"); ?>

</body>

</html>
