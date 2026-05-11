<?php
require_once("inc/config.php");

$sql = "DESCRIBE users";
$result = mysqli_query($conn, $sql);
$columns = [];
while ($row = mysqli_fetch_assoc($result)) {
    $columns[] = $row['Field'];
}

if (!in_array('phone', $columns)) {
    mysqli_query($conn, "ALTER TABLE users ADD COLUMN phone VARCHAR(20) AFTER password");
}

if (!in_array('address', $columns)) {
    mysqli_query($conn, "ALTER TABLE users ADD COLUMN address TEXT AFTER phone");
}

echo "Schema updated successfully (phone, address added if missing)";
?>
