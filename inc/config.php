<?php
// Enable output buffering with Gzip compression
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
    ob_start("ob_gzhandler");
} else {
    ob_start();
}
error_reporting(0);
ini_set('display_errors', 0);
require_once(__DIR__ . "/session.php");

// Secure session cookie settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');

$conn = mysqli_connect("localhost", "root", "", "ma_trends")
    or die("Service temporarily unavailable.");
?>