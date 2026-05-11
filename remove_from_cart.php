<?php
require_once("inc/config.php");

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartId = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;
    $userId = $_SESSION['user']['id'] ?? $_SESSION['admin']['id'];

    if ($cartId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid cart item.']);
        exit();
    }

    $sqlDelete = "DELETE FROM cart WHERE id = $cartId AND user_id = $userId";
    
    if (mysqli_query($conn, $sqlDelete)) {
        // Get new totals
        $sqlTotals = "SELECT SUM(sub_total) as subtotal, COUNT(id) as cart_count FROM cart WHERE user_id = $userId";
        $resTotals = mysqli_query($conn, $sqlTotals);
        $totals = mysqli_fetch_assoc($resTotals);
        $newCartSubtotal = $totals['subtotal'] ?? 0;
        $newCartCount = $totals['cart_count'] ?? 0;

        echo json_encode([
            'success' => true, 
            'cart_subtotal' => number_format($newCartSubtotal, 2),
            'cart_total' => number_format($newCartSubtotal + 200, 2),
            'cart_count' => $newCartCount
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Delete failed.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
