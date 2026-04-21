<?php
function getArray($str)
{
    echo '<pre>';
    print_r($str);
    echo '</pre>';
}

function getSaveValue($conn, $str)
{
    $str = trim($str);
    $str = mysqli_real_escape_string($conn, $str);
    $str = htmlentities($str);
    return $str;
}

function getTotal($conn, $table)
{
    $sql = "SELECT COUNT(*) AS total FROM $table";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    return $data['total'];
}

function getTotalByStatus($conn, $table, $status)
{
    $sql = "SELECT COUNT(*) AS total FROM $table WHERE status='$status'";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    return $data['total'];
}
