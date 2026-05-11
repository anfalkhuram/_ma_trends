<?php
ob_start();
error_reporting(E_ALL);

function respond($data) {
    $out = ob_get_clean();
    if (!empty($out)) {
        $data['debug_output'] = $out;
    }
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_CORE_ERROR || $error['type'] === E_COMPILE_ERROR)) {
        respond(['success' => false, 'message' => 'PHP Fatal Error: ' . $error['message'] . ' in ' . $error['file'] . ' on line ' . $error['line']]);
    }
});

try {
    require_once("inc/config.php");

    if (!isLoggedIn()) {
        respond(['success' => false, 'message' => 'Please log in to place an order.']);
    }

    $userId = $_SESSION['user']['id'] ?? $_SESSION['admin']['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Ensure tables exist
    $createOrdersTable = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        email VARCHAR(255) NOT NULL,
        address TEXT NOT NULL,
        city VARCHAR(100) NOT NULL,
        region VARCHAR(100) NOT NULL,
        postalcode VARCHAR(50) NOT NULL,
        total DECIMAL(10, 2) NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        shipping DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        receipt_image VARCHAR(255) DEFAULT NULL,
        status INT NOT NULL DEFAULT 0,
        order_confirmation INT NOT NULL DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    mysqli_query($conn, $createOrdersTable);

    $createOrderDetailsTable = "CREATE TABLE IF NOT EXISTS order_details (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        product_name VARCHAR(255) NOT NULL,
        qty INT NOT NULL,
        sub_total DECIMAL(10, 2) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    mysqli_query($conn, $createOrderDetailsTable);

    // 2. Validate Cart is not empty
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

    // 4. Handle File Upload (if Easypaisa or Jazzcash)
    $receiptImageName = null;
    if ($paymentMethod === 'easypaisa' || $paymentMethod === 'jazzcash') {
        if (!isset($_FILES['paymentScreenshot']) || $_FILES['paymentScreenshot']['error'] !== UPLOAD_ERR_OK) {
            respond(['success' => false, 'message' => 'Please upload a valid receipt screenshot.']);
        }

        $uploadDir = 'assets/img/receipts/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . '_' . basename($_FILES['paymentScreenshot']['name']);
        $targetFilePath = $uploadDir . $fileName;
        
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = array('jpg', 'png', 'jpeg', 'gif', 'webp');
        if (!in_array($fileType, $allowedTypes)) {
            respond(['success' => false, 'message' => 'Only JPG, JPEG, PNG, GIF, & WEBP files are allowed.']);
        }

        if (move_uploaded_file($_FILES["paymentScreenshot"]["tmp_name"], $targetFilePath)) {
            $receiptImageName = $fileName;
        } else {
            respond(['success' => false, 'message' => 'Sorry, there was an error uploading your receipt.']);
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
            $prodId = $item['product_id'];
            $prodName = mysqli_real_escape_string($conn, $item['product_name']);
            $qty = $item['quantity'];
            $subTotal = $item['sub_total'];

            $sqlInsertDetail = "INSERT INTO order_details (user_id, order_id, product_id, product_name, qty, sub_total, created_at) 
                                VALUES ($userId, $orderId, $prodId, '$prodName', $qty, $subTotal, NOW())";
            
            if (!mysqli_query($conn, $sqlInsertDetail)) {
                $successDetails = false;
            }
        }

        if ($successDetails) {
            // Clear the cart
            mysqli_query($conn, "DELETE FROM cart WHERE user_id = $userId");
            respond(['success' => true, 'message' => 'Order placed successfully.']);
        } else {
            respond(['success' => false, 'message' => 'Order placed, but some details failed to save.']);
        }

    } else {
        respond(['success' => false, 'message' => 'Database error while placing order: ' . mysqli_error($conn)]);
    }
} else {
    respond(['success' => false, 'message' => 'Invalid request.']);
}

} catch (\Throwable $e) {
    respond(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
}
?>
