<?php
session_start();

require_once("inc/config.php");

/* ===============================
   PAGE SEO
================================= */
$pageTitle = "Checkout | MATrends";
$description = "Complete your MATrends order with delivery details, payment method and final review.";
$keywords = "checkout, order, MATrends";
$author = "MATrends";
$robots = "noindex,nofollow";

/* ===============================
   USER ID
================================= */
$user_id = isset($_SESSION['cart_user_id']) ? (int) $_SESSION['cart_user_id'] : 1;
$_SESSION['cart_user_id'] = $user_id;

/* ===============================
   DEFAULT VALUES
================================= */
$flashMessage = "";
$flashType = "success";
$formErrors = [];

$full_name = "";
$phone = "";
$email = "";
$city = "";
$address = "";
$payment_method = "cash_on_delivery";
$notes = "";

/* ===============================
   FETCH CART ITEMS
================================= */
$cartSql = "SELECT cart.*, 
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

$cartResult = mysqli_query($conn, $cartSql);
$cartItems = [];
$grandTotal = 0;
$itemCount = 0;

while ($row = mysqli_fetch_assoc($cartResult)) {
    $cartItems[] = $row;
    $grandTotal += (int) $row['sub_total'];
    $itemCount += (int) $row['qty'];
}

$shipping = 250;

if ($grandTotal == 0) {
    $shipping = 0;
}

$totalPayable = $grandTotal + $shipping;

function price($amount)
{
    return "Rs. " . number_format((float) $amount, 0);
}

/* ===============================
   PLACE ORDER
================================= */
if (isset($_POST['place_order'])) {

    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $payment_method = trim($_POST['payment_method'] ?? 'cash_on_delivery');
    $notes = trim($_POST['notes'] ?? '');

    if ($itemCount == 0) {
        $formErrors[] = "Your cart is empty. Please add products before placing an order.";
    }

    if ($full_name == '') {
        $formErrors[] = "Full name is required.";
    }

    if ($phone == '') {
        $formErrors[] = "Phone number is required.";
    }

    if ($email == '') {
        $formErrors[] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formErrors[] = "Please enter a valid email address.";
    }

    if ($city == '') {
        $formErrors[] = "City is required.";
    }

    if ($address == '') {
        $formErrors[] = "Delivery address is required.";
    }

    $allowedPaymentMethods = ['cash_on_delivery', 'bank_transfer'];

    if (!in_array($payment_method, $allowedPaymentMethods, true)) {
        $formErrors[] = "Please select a valid payment method.";
    }

    if (empty($formErrors)) {
        mysqli_begin_transaction($conn);

        try {
            $safeFullName = mysqli_real_escape_string($conn, $full_name);
            $safePhone = mysqli_real_escape_string($conn, $phone);
            $safeEmail = mysqli_real_escape_string($conn, $email);
            $safeCity = mysqli_real_escape_string($conn, $city);
            $safeAddress = mysqli_real_escape_string($conn, $address);
            $safePaymentMethod = mysqli_real_escape_string($conn, $payment_method);
            $safeNotes = mysqli_real_escape_string($conn, $notes);

            $orderSql = "INSERT INTO orders
                        (user_id, full_name, phone, email, city, address, payment_method, notes, subtotal, shipping, total)

                        VALUES
                        ('$user_id', '$safeFullName', '$safePhone', '$safeEmail', '$safeCity', '$safeAddress', '$safePaymentMethod', '$safeNotes', '$grandTotal', '$shipping', '$totalPayable')";

            if (!mysqli_query($conn, $orderSql)) {
                throw new Exception("Order could not be saved.");
            }

            $order_id = mysqli_insert_id($conn);

            foreach ($cartItems as $item) {

                $product_id = (int) $item['product_id'];
                $item_price = (int) $item['price'];
                $qty = (int) $item['qty'];
                $sub_total = (int) $item['sub_total'];

                $orderItemSql = "INSERT INTO order_items
                                (order_id, product_id, price, qty, sub_total)

                                VALUES
                                ('$order_id', '$product_id', '$item_price', '$qty', '$sub_total')";

                if (!mysqli_query($conn, $orderItemSql)) {
                    throw new Exception("Order items could not be saved.");
                }
            }

            $clearCartSql = "DELETE FROM cart WHERE user_id='$user_id'";

            if (!mysqli_query($conn, $clearCartSql)) {
                throw new Exception("Cart could not be cleared after order placement.");
            }

            mysqli_commit($conn);

            $_SESSION['order_success_message'] = "Order placed successfully. Thank you for shopping with MATrends.";
            header("Location: cart.php");
            exit();
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $flashMessage = $e->getMessage();
            $flashType = "danger";
        }
    } else {
        $flashMessage = implode(" ", $formErrors);
        $flashType = "danger";
    }
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
                        Secure Checkout
                    </span>

                    <h1 class="display-6 fw-bold mb-2 text-white">
                        Complete your order
                    </h1>

                    <p class="ma-muted mb-0">
                        Enter your delivery details, review your items and place the order confidently.
                    </p>
                </div>

                <div class="ma-card px-4 py-3 text-center">
                    <div class="small ma-muted">Items in your order</div>
                    <div class="h3 mb-0 fw-bold">
                        <?php echo $itemCount; ?>
                    </div>
                </div>

            </div>

            <?php if ($flashMessage != '') { ?>
                <div class="alert alert-<?php echo $flashType; ?> mb-4">
                    <?php echo htmlspecialchars($flashMessage); ?>
                </div>
            <?php } ?>

            <div class="row g-4">

                <!-- LEFT -->
                <div class="col-lg-7">

                    <div class="ma-card p-4 p-md-5">

                        <h2 class="h3 fw-bold mb-3">
                            Billing & Shipping Details
                        </h2>

                        <p class="ma-muted mb-4">
                            Please provide accurate contact and delivery information so we can process your order smoothly.
                        </p>

                        <form method="POST" class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($full_name); ?>" placeholder="Enter your full name">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($phone); ?>" placeholder="03XXXXXXXXX">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter your email">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($city); ?>" placeholder="Enter your city">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Delivery Address</label>
                                <textarea name="address" class="form-control" rows="4" placeholder="House number, street, area, nearby landmark"><?php echo htmlspecialchars($address); ?></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Payment Method</label>
                                <select name="payment_method" class="form-select">
                                    <option value="cash_on_delivery" <?php echo ($payment_method == 'cash_on_delivery') ? 'selected' : ''; ?>>Cash on Delivery</option>
                                    <option value="bank_transfer" <?php echo ($payment_method == 'bank_transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Order Notes</label>
                                <input type="text" name="notes" class="form-control" value="<?php echo htmlspecialchars($notes); ?>" placeholder="Optional instructions">
                            </div>

                            <div class="col-12">

                                <div class="ma-card p-3">

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
                                        <span class="fw-semibold">Payable Amount</span>
                                        <span class="h5 fw-bold mb-0"><?php echo price($totalPayable); ?></span>
                                    </div>

                                </div>

                            </div>

                            <div class="col-12 d-flex flex-wrap gap-2">

                                <button type="submit" name="place_order" class="btn btn-ma" <?php echo ($itemCount == 0) ? 'disabled' : ''; ?>>
                                    Place Order
                                </button>

                                <a href="cart.php" class="btn btn-ma-outline">
                                    Back to Cart
                                </a>

                            </div>

                        </form>

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="col-lg-5">

                    <div class="ma-card p-4 mb-4">

                        <h2 class="h4 fw-bold mb-3">
                            Order Summary
                        </h2>

                        <?php if ($itemCount == 0) { ?>

                            <div class="text-center py-4">
                                <div class="h6 fw-semibold mb-2">Your cart is empty</div>
                                <p class="ma-muted mb-3">Add products to continue with checkout.</p>
                                <a href="shop" class="btn btn-ma">Continue Shopping</a>
                            </div>

                        <?php } else { ?>

                            <div class="d-flex flex-column gap-3">

                                <?php foreach ($cartItems as $item) { ?>

                                    <?php
                                    $imagePath = !empty($item['image'])
                                        ? "./admins/assets/images/products/" . $item['image']
                                        : "assets/img/ma_trends_ill.png";
                                    ?>

                                    <div class="border rounded-4 p-3">

                                        <div class="d-flex gap-3 align-items-center">

                                            <img
                                                src="<?php echo htmlspecialchars($imagePath); ?>"
                                                alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                class="rounded-3"
                                                style="width: 72px; height: 72px; object-fit: cover;">

                                            <div class="flex-grow-1">
                                                <div class="fw-semibold mb-1">
                                                    <?php echo htmlspecialchars($item['name']); ?>
                                                </div>
                                                <div class="small ma-muted mb-1">
                                                    Qty: <?php echo (int) $item['qty']; ?> x <?php echo price($item['price']); ?>
                                                </div>
                                                <div class="fw-semibold">
                                                    <?php echo price($item['sub_total']); ?>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                <?php } ?>

                            </div>

                            <hr class="border ma-border my-4">

                            <div class="d-flex justify-content-between mb-2">
                                <span class="ma-muted">Subtotal</span>
                                <span><?php echo price($grandTotal); ?></span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="ma-muted">Shipping</span>
                                <span><?php echo price($shipping); ?></span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fw-semibold">Grand Total</span>
                                <span class="h5 fw-bold mb-0"><?php echo price($totalPayable); ?></span>
                            </div>

                        <?php } ?>

                    </div>

                    <div class="ma-card p-4">

                        <h3 class="h5 fw-bold mb-3">
                            Why order with us?
                        </h3>

                        <div class="ma-muted small">
                            Secure checkout, clear pricing, fast shipping and a straightforward order flow designed like a professional ecommerce store.
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
