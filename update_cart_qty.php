<?php
require_once("inc/config.php");

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartId = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : ''; // 'plus' or 'minus'
    $userId = $_SESSION['user']['id'] ?? $_SESSION['admin']['id'];

    if ($cartId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid cart item.']);
        exit();
    }

    // Get current item
    $sql = "SELECT * FROM cart WHERE id = $cartId AND user_id = $userId LIMIT 1";
    $res = mysqli_query($conn, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        $item = mysqli_fetch_assoc($res);
        $qty = $item['quantity'];
        $price = $item['price'];

        if ($action === 'plus') {
            $qty++;
        } elseif ($action === 'minus') {
            $qty = max(1, $qty - 1);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action.']);
            exit();
        }

        $newSubTotal = $qty * $price;
        $sqlUpdate = "UPDATE cart SET quantity = $qty, sub_total = $newSubTotal WHERE id = $cartId";
        
        if (mysqli_query($conn, $sqlUpdate)) {
            // Get new totals
            $sqlTotals = "SELECT SUM(sub_total) as subtotal FROM cart WHERE user_id = $userId";
            $resTotals = mysqli_query($conn, $sqlTotals);
            $totals = mysqli_fetch_assoc($resTotals);
            $newCartSubtotal = $totals['subtotal'] ?? 0;

            echo json_encode([
                'success' => true, 
                'quantity' => $qty, 
                'line_total' => number_format($newSubTotal, 2),
                'cart_subtotal' => number_format($newCartSubtotal, 2),
                'cart_total' => number_format($newCartSubtotal + 200, 2)
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Update failed.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
