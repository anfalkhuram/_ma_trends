<?php
ob_start();
error_reporting(0);

function respond($data) {
    $out = ob_get_clean();
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        respond(['success' => false, 'message' => 'Something went wrong. Please try again.']);
    }
});

try {
    require_once("inc/config.php");

    if (!isLoggedIn()) {
        respond(['success' => false, 'message' => 'Please log in to place an order.']);
    }

    $userId = $_SESSION['user']['id'] ?? $_SESSION['admin']['id'] ?? 0;
    
    // Check if user is verified
    $sqlVerify = "SELECT is_verified FROM users WHERE id = $userId";
    $resVerify = mysqli_query($conn, $sqlVerify);
    if ($resVerify && mysqli_num_rows($resVerify) > 0) {
        $rowVerify = mysqli_fetch_assoc($resVerify);
        if ($rowVerify['is_verified'] != 1) {
            respond(['success' => false, 'message' => 'Please verify your email to place an order.']);
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sqlCart = "SELECT c.*, p.name as product_name FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $userId";
        $resCart = mysqli_query($conn, $sqlCart);
    if (!$resCart || mysqli_num_rows($resCart) === 0) {
        respond(['success' => false, 'message' => 'Your cart is empty.']);
    }

    // 3. Collect Data
    $name = mysqli_real_escape_string($conn, $_POST['shipping_name'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['shipping_email'] ?? '');
    $phone = mysqli_real_escape_string($conn, $_POST['shipping_phone'] ?? '');
    $city = mysqli_real_escape_string($conn, $_POST['shipping_city'] ?? '');
    $address = mysqli_real_escape_string($conn, $_POST['shipping_address'] ?? '');
    $region = mysqli_real_escape_string($conn, $_POST['shipping_region'] ?? '');
    $postalcode = mysqli_real_escape_string($conn, $_POST['shipping_postalcode'] ?? '');
    $paymentMethod = mysqli_real_escape_string($conn, $_POST['paymentMethod'] ?? '');

    if (empty($name) || empty($email) || empty($phone) || empty($city) || empty($address) || empty($region) || empty($postalcode) || empty($paymentMethod)) {
        respond(['success' => false, 'message' => 'Please fill all required shipping fields.']);
    }

    // Calculate total from cart
    $total = 0;
    $cartItems = [];
    while ($item = mysqli_fetch_assoc($resCart)) {
        $total += $item['sub_total'];
        $cartItems[] = $item;
    }
    
    $shippingCost = 0.00; // Currently free delivery based on frontend
    $total += $shippingCost;

    // 4. Handle File Upload (if Easypaisa, Jazzcash, or Bank Transfer)
    $receiptImageName = null;
    if ($paymentMethod === 'easypaisa' || $paymentMethod === 'jazzcash' || $paymentMethod === 'bank_transfer') {
        if (!isset($_FILES['paymentScreenshot']) || $_FILES['paymentScreenshot']['error'] !== UPLOAD_ERR_OK) {
            respond(['success' => false, 'message' => 'Please upload a valid receipt screenshot.']);
        }

        // Verify real MIME type — do NOT trust the extension
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $realMime = finfo_file($finfo, $_FILES['paymentScreenshot']['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
        if (!isset($allowedMimes[$realMime])) {
            respond(['success' => false, 'message' => 'Only image files (JPG, PNG, GIF, WEBP) are allowed.']);
        }

        $uploadDir = 'assets/img/receipts/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Random filename — never use the original filename
        $ext      = $allowedMimes[$realMime];
        $fileName = bin2hex(random_bytes(16)) . '.' . $ext;
        $targetFilePath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['paymentScreenshot']['tmp_name'], $targetFilePath)) {
            $receiptImageName = $fileName;
        } else {
            respond(['success' => false, 'message' => 'Error uploading receipt. Please try again.']);
        }
    }

    // 5. Insert into orders table
    // Note: status is set to 0 by default based on table definition, but we can explicitly set it to 0
    $sqlInsertOrder = "INSERT INTO orders (user_id, name, phone, email, address, city, region, postalcode, total, payment_method, shipping, receipt_image, status, created_at) 
                       VALUES ($userId, '$name', '$phone', '$email', '$address', '$city', '$region', '$postalcode', $total, '$paymentMethod', $shippingCost, " . ($receiptImageName ? "'$receiptImageName'" : "NULL") . ", 0, NOW())";
    
    if (mysqli_query($conn, $sqlInsertOrder)) {
        $orderId = mysqli_insert_id($conn);

        // 6. Insert into order_details and delete from cart
        $successDetails = true;
        foreach ($cartItems as $item) {
            $prodId   = (int)$item['product_id'];
            $prodName = mysqli_real_escape_string($conn, $item['product_name']);
            $qty      = (int)$item['quantity'];
            $subTotal = (float)$item['sub_total'];

            $sqlInsertDetail = "INSERT INTO order_details (user_id, order_id, product_id, product_name, qty, sub_total, created_at)
                                VALUES ($userId, $orderId, $prodId, '$prodName', $qty, $subTotal, NOW())";
            if (!mysqli_query($conn, $sqlInsertDetail)) {
                $successDetails = false;
            }
        }

        if ($successDetails) {
            mysqli_query($conn, "DELETE FROM cart WHERE user_id = $userId");
            
            // Send Transactional Emails
            require_once('inc/email_functions.php');
            $host = "http" . (isset($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['HTTP_HOST'];
            
            $emailData = [
                'name' => $name,
                'orderId' => $orderId,
                'items' => $cartItems,
                'shippingCost' => $shippingCost,
                'total' => $total,
                'paymentMethod' => $paymentMethod,
                'address' => $address,
                'city' => $city,
                'region' => $region,
                'postalcode' => $postalcode,
                'ordersUrl' => $host . '/orders.php',
                'adminOrdersUrl' => $host . '/admins/orders.php',
                'customerEmail' => $email,
                'phone' => $phone
            ];
            
            // Customer email
            sendTransactionalEmail($email, "Your MATrends Order Has Been Placed", 'order_placed', $emailData, 'user', $orderId);
            
            // Admin email
            sendTransactionalEmail(SMTP_USERNAME, "New Order Received on MATrends", 'admin_new_order', $emailData, 'admin', $orderId);
            
            respond(['success' => true, 'message' => 'Order placed successfully.']);
        } else {
            respond(['success' => false, 'message' => 'Order placed but some items failed to save. Contact support.']);
        }
    } else {
        respond(['success' => false, 'message' => 'Could not place your order. Please try again.']);
    }
} else {
    respond(['success' => false, 'message' => 'Invalid request.']);
}

} catch (\Throwable $e) {
    respond(['success' => false, 'message' => 'Something went wrong. Please try again.']);
}
?>
