<?php
// Suppress display_errors FIRST — any PHP notice would corrupt the JSON response
ini_set('display_errors', 0);
error_reporting(0);

// Buffer all output so stray text cannot leak before our JSON header
ob_start();

require_once('../inc/config.php');

// Discard any output that config.php may have produced (e.g. from display_errors)
ob_clean();

header('Content-Type: application/json');

// Must be a logged-in user (not admin) to submit a review
if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to submit a review.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$userId    = (int) $_SESSION['user']['id'];
$userName  = mysqli_real_escape_string($conn, trim($_SESSION['user']['name']));
$productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
$rating    = isset($_POST['rating'])     ? (int) $_POST['rating']     : 0;
$feedback  = isset($_POST['feedback'])   ? mb_substr(trim($_POST['feedback']), 0, 1000) : '';

// Validate inputs
if ($productId <= 0 || $rating < 1 || $rating > 5 || empty($feedback)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields correctly.']);
    exit;
}

$safeFeedback = mysqli_real_escape_string($conn, $feedback);

// Prevent duplicate reviews (one per user per product)
$checkSql = "SELECT id FROM product_feedback WHERE user_id = $userId AND product_id = $productId LIMIT 1";
$checkRes = mysqli_query($conn, $checkSql);
if ($checkRes && mysqli_num_rows($checkRes) > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already reviewed this product.']);
    exit;
}

// Insert review — status 0 = pending admin approval
$insertSql = "INSERT INTO product_feedback (user_id, product_id, name, rating, feedback, status, created_at)
              VALUES ($userId, $productId, '$userName', $rating, '$safeFeedback', 0, NOW())";
if (!mysqli_query($conn, $insertSql)) {
    echo json_encode(['success' => false, 'message' => 'Could not submit review. Please try again.']);
    exit;
}

// Recalculate average rating from approved reviews and write back to products table
$avgSql = "SELECT ROUND(AVG(rating), 1) AS avg_rating FROM product_feedback WHERE product_id = $productId AND status = 1";
$avgRes = mysqli_query($conn, $avgSql);
if ($avgRes) {
    $avgRow = mysqli_fetch_assoc($avgRes);
    if ($avgRow['avg_rating'] !== null) {
        mysqli_query($conn, "UPDATE products SET ratings = " . (float)$avgRow['avg_rating'] . " WHERE id = $productId");
    }
}

echo json_encode([
    'success' => true,
    'message' => 'Thank you! Your review has been submitted and is pending approval.'
]);
exit;
