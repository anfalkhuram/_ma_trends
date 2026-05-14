<?php
require_once("inc/config.php");

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity  = isset($_POST['quantity'])   ? max(1, min(99, intval($_POST['quantity']))) : 1;
    $userId = $_SESSION['user']['id'] ?? $_SESSION['admin']['id'];

    if ($productId <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product or quantity.']);
        exit();
    }

    // Get product price
    $sqlPrice = "SELECT price FROM products WHERE id = $productId LIMIT 1";
    $resPrice = mysqli_query($conn, $sqlPrice);
    if ($resPrice && mysqli_num_rows($resPrice) > 0) {
        $product = mysqli_fetch_assoc($resPrice);
        $price = $product['price'];
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
        exit();
    }

    // Check if already in cart
    $sqlCheck = "SELECT id, quantity FROM cart WHERE user_id = $userId AND product_id = $productId LIMIT 1";
    $resCheck = mysqli_query($conn, $sqlCheck);

    if ($resCheck && mysqli_num_rows($resCheck) > 0) {
        $cartItem = mysqli_fetch_assoc($resCheck);
        $newQty = $cartItem['quantity'] + $quantity;
        $newSubTotal = $newQty * $price;
        $sqlAction = "UPDATE cart SET quantity = $newQty, sub_total = $newSubTotal WHERE id = " . $cartItem['id'];
    } else {
        $subTotal = $quantity * $price;
        $sqlAction = "INSERT INTO cart (user_id, product_id, price, quantity, sub_total) VALUES ($userId, $productId, $price, $quantity, $subTotal)";
    }

    if (mysqli_query($conn, $sqlAction)) {
        $sqlTotal = "SELECT COUNT(id) as total FROM cart WHERE user_id = $userId";
        $resTotal = mysqli_query($conn, $sqlTotal);
        $totalRow = mysqli_fetch_assoc($resTotal);
        $newTotal = $totalRow['total'] ?? 0;

        echo json_encode(['success' => true, 'message' => 'Product added to cart!', 'cart_count' => $newTotal]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Could not add to cart. Please try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
