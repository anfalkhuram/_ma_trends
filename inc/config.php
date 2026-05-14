<?php
error_reporting(0);
ini_set('display_errors', 0);
require_once(__DIR__ . "/session.php");

// Secure session cookie settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');

$conn = mysqli_connect("localhost", "root", "", "ma_trends")
    or die("Service temporarily unavailable.");
?>