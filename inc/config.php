<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once(__DIR__ . "/session.php");
$conn = mysqli_connect("localhost", "root", "", "ma_trends") or die("Connection Failed: " . mysqli_connect_error());
?>