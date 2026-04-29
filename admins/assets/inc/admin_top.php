<?php
require_once('./assets/inc/config.php');
require_once('./assets/inc/functions.php');

// fetching page name
$pageName = ucwords(str_replace("-", " ", basename($_SERVER['PHP_SELF'], ".php")));
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin <?php echo $pageName ?></title>
    <meta name="description" content="Admin dashboard UI — MA Trends." />
    <link rel="shortcut icon" href="./assets/images/ma_trends_ill.png" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap"
        rel="stylesheet" />
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" /> -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" /> -->
    
    <link href="./assets/css/all.min.css" rel="stylesheet" />
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="./assets/css/styles.css" rel="stylesheet" />
</head>