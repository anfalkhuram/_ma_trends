<?php
ini_set('display_errors', 0);
error_reporting(0);

require_once('./assets/inc/config.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false]);
    exit;
}

$action    = isset($_POST['action'])     ? trim($_POST['action'])     : '';
$id        = isset($_POST['id'])         ? (int)$_POST['id']         : 0;
$newStatus = isset($_POST['new_status']) ? (int)$_POST['new_status'] : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'msg' => 'bad id']);
    exit;
}

if ($action === 'set_status') {
    $ok = mysqli_query($conn, "UPDATE product_feedback SET status = $newStatus WHERE id = $id");
    echo json_encode(['success' => (bool)$ok]);
    exit;
}

if ($action === 'delete') {
    $ok = mysqli_query($conn, "DELETE FROM product_feedback WHERE id = $id");
    echo json_encode(['success' => (bool)$ok]);
    exit;
}

echo json_encode(['success' => false, 'msg' => 'unknown action']);
