<?php
error_reporting(0);
ini_set('display_errors', 0);
require_once(__DIR__ . "/../../../inc/session.php");
$conn = mysqli_connect("localhost", "root", "", "ma_trends")
    or die("Service temporarily unavailable.");
?>