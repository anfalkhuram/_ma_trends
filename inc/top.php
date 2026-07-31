<?php
require_once("config.php");
$pageName = ucwords(str_replace("-", " ", basename($_SERVER['PHP_SELF'], ".php")));

if($pageName == 'Index'){
  $pageName = 'Home';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="description" content="<?php echo $description; ?>" />
  <meta name="keywords" content="<?php echo $keywords; ?>" />
  <meta name="author" content="<?php echo $author; ?>" />
  <meta name="robots" content="<?php echo $robots; ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <meta property="og:title" content="<?php echo $ogTitle; ?>" />
  <meta property="og:description" content="<?php echo $ogDescription; ?>" />
  <meta property="og:type" content="<?php echo $ogType; ?>" />
  <meta property="og:url" content="<?php echo $ogUrl; ?>" />

  <title><?php echo $pageTitle ?></title>


  <link rel="shortcut icon" href="assets/img/ma_trends_ill.webp" type="image/x-icon">
  
  <!-- Preload critical assets -->
  <link rel="preload" href="assets/img/hero-image.webp" as="image" fetchpriority="high">
  
  <!-- Google Fonts (Poppins + Inter) -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap"
    rel="stylesheet" />

  <!-- Bootstrap 5 -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" /> -->
  <link href="assets/css/all.min.css" rel="stylesheet" />
  <link href="assets/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Global CSS -->
  <link href="assets/css/styles.css?v=<?php echo time(); ?>" rel="stylesheet" />

</head>