 <?php
$pageTitle = "Couple Collection – Rings & Accessories for Couples | MATrends";

$description = "Shop couple collection at MATrends. Discover matching couple rings, bracelets and stylish accessories designed for him and her at affordable prices.";

$keywords = "couple collection, couple rings, matching rings for couples, couple accessories, his and hers rings, couple jewelry Pakistan, MATrends couple collection";

$author = "MATrends";
$robots = "index, follow";

$ogTitle = "Couple Collection – Matching Rings & Accessories | MATrends";
$ogDescription = "Explore matching couple rings and accessories at MATrends. Stylish designs for him and her.";
$ogType = "website";
$ogUrl = "https://www.matrends.store/shop";


    require_once("inc/top.php");
    isset($_GET['id']) ? $_GET['id'] : null;
    if ($_GET['id'] != null) {
        $categoryId = $_GET['id'];
        $sqlCategory = "SELECT name FROM categories WHERE id = $categoryId;";
        $resultCategory = mysqli_query($conn, $sqlCategory);
        $category = mysqli_fetch_assoc($resultCategory);
    } else {
        // Redirect to shop page if no category ID is provided
        header("Location: shop");
        exit();
    }
    ?>

 <body>
     <?php
        require_once("inc/navbar.php");
        ?>

     <header class="ma-hero pb-4">
         <div class="container">
             <div class="d-flex flex-wrap align-items-end justify-content-between gap-3">
                 <div>
                     <div class="ma-kicker mb-2">Category</div>
                     <h1 class="h2 fw-bold mb-2 text-capitalize"><?php echo $category['name']; ?></h1>
                     <div class="ma-muted">Minimal, sport, and premium styles.</div>
                 </div>
                 <a class="btn btn-ma-outline" href="shop">All products</a>
             </div>
         </div>
     </header>

     <main class="pb-5">
         <div class="container">
             <div class="row g-4">

                 <?php
                    $sqlProducts = "SELECT * FROM products WHERE category_id = $categoryId AND status = 1;";
                    $resultProducts = mysqli_query($conn, $sqlProducts);
                    if (mysqli_num_rows($resultProducts) > 0) {
                        while ($rowProduct = mysqli_fetch_assoc($resultProducts)) {
                            $productIDForDetails = $rowProduct['id'];
                            $sqlProductDetails = "SELECT * FROM product_details WHERE product_id = $productIDForDetails;";
                            $resultProductDetails = mysqli_query($conn, $sqlProductDetails);
                            $productDetails = mysqli_fetch_assoc($resultProductDetails);
                    ?>
                         <div class="col-12 col-md-4 col-xl-3">
                             <div class="ma-card ma-product">

                                 <img class="ma-card-img" src="./admins/assets/images/products/<?php echo $productDetails['image']; ?>" alt="<?php echo $rowProduct['name']; ?>" />
                                 <div class="p-3">
                                     <div class="d-flex flex-wrap gap-2 mb-2">

                                         <?php

                                            if ($productDetails['label'] == 1) {
                                                echo '<span class="badge badge-ma rounded-pill">Normal</span>';
                                            } elseif ($productDetails['label'] == 2) {
                                                echo '<span class="badge badge-trending rounded-pill">🔥 Trending</span>';
                                            } else {
                                                echo '<span class="badge badge-ma rounded-pill">New Drop</span>';
                                            }
                                            ?>

                                     </div>
                                     <div class="fw-semibold"><?php echo $rowProduct['name']; ?></div>
                                     <div class="ma-muted small">
                                         <?php
                                            if ($productDetails['gender'] == 1) {
                                                echo "Men";
                                            } else if ($productDetails['gender'] == 2) {
                                                echo "Women";
                                            } else {
                                                echo "Universal";
                                            }
                                            ?>
                                         • <?php echo $productDetails['options']; ?>-><?php echo $productDetails['value']; ?></div>
                                     <div class="d-flex align-items-center justify-content-between mt-2">
                                         <div class="ma-price">Rs.<?php echo $rowProduct['price']; ?></div>
                                         <div class="d-flex">
                                             <button class="btn btn-sm btn-ma me-1 js-add-to-cart" data-product-id="<?php echo $rowProduct['id']; ?>">Buy</button>
                                             <a class="btn btn-sm btn-ma-outline" href="products?id=<?php echo $rowProduct['id']; ?>">View</a>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                 <?php
                        }
                    } else {
                        echo '<div class="col-12"><p class="text-center ma-muted">No products available at the moment.</p></div>';
                    }
                    ?>



             </div>
         </div>
     </main>

     <?php
        require_once("inc/footer.php");
        ?>

     <?php
        require_once("inc/bottom.php");
        ?>
    
 </body>

 </html>