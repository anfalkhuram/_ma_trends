<?php
require_once('inc/config.php');

// Unset session variables
session_unset();
session_write_close();
header("Location: index");
exit();
?>
