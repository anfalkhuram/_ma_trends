<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper: check if the user is logged in (either as user or admin)
function isLoggedIn() {
    return (isset($_SESSION['user']['id']) && !empty($_SESSION['user']['id'])) || 
           (isset($_SESSION['admin']['id']) && !empty($_SESSION['admin']['id']));
}

// Helper: check if specifically an admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin']['id']) && !empty($_SESSION['admin']['id']);
}

// Helper: redirect to login with a return URL
function requireLogin($returnUrl = '') {
    if (!isLoggedIn()) {
        $redirect = $returnUrl ? '?redirect=' . urlencode($returnUrl) : '';
        session_write_close();
        header("Location: login" . $redirect);
        exit();
    }
}

// Helper: redirect to admin login if not an admin
function requireAdmin() {
    if (!isAdminLoggedIn()) {
        session_write_close();
        header("Location: ../login");
        exit();
    }
}
?>
