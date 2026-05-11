<?php
$pageTitle = "Shop Rings, Watches, Bags & Accessories | MATrends";

$description = "Browse all products at MATrends. Shop trending rings, watches, bags, jewelry and fashion accessories with stylish designs and affordable prices.";

$keywords = "shop rings, shop watches, fashion accessories Pakistan, buy jewelry online, MATrends shop, bags online Pakistan, couple collection, trendy accessories";

$author = "MATrends";
$robots = "index, follow";

$ogTitle = "Shop Rings, Watches, Bags & Accessories | MATrends";
$ogDescription = "Explore all trending products at MATrends. Rings, watches, bags and accessories in modern styles.";
$ogType = "website";
$ogUrl = "https://www.matrends.store/shop";
require_once('inc/top.php');
?>

<body>
    <?php
    require_once('inc/navbar.php');
    ?>

    <header class="ma-hero pb-4">
        <div class="container">
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-3">
                <div>
                    <div class="ma-kicker mb-2">Shop</div>
                    <h1 class="h2 fw-bold mb-2">Products</h1>
                    <div class="ma-muted">Filter by category and gender.</div>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-ma" href="index">Home</a>
                </div>
            </div>
        </div>
    </header>

    <main class="pb-5">
        <div class="container">
            <!-- Filters -->
            <div class="ma-card p-3 p-md-4 mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label ma-muted">Category</label>
                        <select class="form-select" id="filterCategory">
                            <option value="all" selected>All</option>
                            <?php
                            $sqlCategories = "SELECT * FROM categories where status = 1";
                            $resultCategories = mysqli_query($conn, $sqlCategories);
                            if (mysqli_num_rows($resultCategories) > 0) {
                                while ($rowCategory = mysqli_fetch_assoc($resultCategories)) {
                            ?>
                                    <option value="<?php echo $rowCategory['name']; ?>" class="text-capitalize"><?php echo $rowCategory['name']; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label ma-muted">Men / Women / Universal</label>
                        <select class="form-select" id="filterGender">
                            <option value="all" selected>All</option>
                            <option value="men">Men</option>
                            <option value="women">Women</option>
                            <option value="universal">Universal</option>
                        </select>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <a href="#" class="btn btn-ma-outline js-clear-filters">Clear filters</a>
                    </div>
                </div>
            </div>

            <!-- Product grid -->
            <div class="row g-4" id="trending">
                <!-- Each item uses .js-product + data attributes for filter -->
                <?php
                $sqlProducts = "SELECT * FROM products where status=1";
                $resultProducts = mysqli_query($conn, $sqlProducts);
                if (mysqli_num_rows($resultProducts) > 0) {
                    while ($rowProduct = mysqli_fetch_assoc($resultProducts)) {
                        $productCategoryId = $rowProduct['category_id'];
                        $sqlCategory = "SELECT * FROM categories WHERE id = $productCategoryId;";
                        $resultCategory = mysqli_query($conn, $sqlCategory);
                        $category = mysqli_fetch_assoc($resultCategory);

                        $productIDForDetails = $rowProduct['id'];
                        $sqlProductDetails = "SELECT * FROM product_details WHERE product_id = $productIDForDetails;";
                        $resultProductDetails = mysqli_query($conn, $sqlProductDetails);
                        $productDetails = mysqli_fetch_assoc($resultProductDetails);
                ?>
                        <div class="col-6 col-md-4 col-xl-3 js-product" data-category="<?php echo $category['name']; ?>" data-gender="<?php
                                                                                                                                        if ($productDetails['gender'] == 1) {
                                                                                                                                            echo 'men';
                                                                                                                                        } elseif ($productDetails['gender'] == 2) {
                                                                                                                                            echo 'women';
                                                                                                                                        } else {
                                                                                                                                            echo 'universal';
                                                                                                                                        }
                                                                                                                                        ?>">
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
                                    <div class="ma-muted small"><?php
                                                                if ($productDetails['gender'] == 1) {
                                                                    echo 'Men';
                                                                } elseif ($productDetails['gender'] == 2) {
                                                                    echo 'Women';
                                                                } else {
                                                                    echo 'Universal';
                                                                }

                                                                ?> • <span class="text-capitalize"><?php echo $category['name']; ?></span></div>
                                    <div class="d-flex align-items-center justify-content-between mt-2">
                                        <div class="ma-price">Rs. <?php echo $rowProduct['price']; ?></div>
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

            <!-- Quick View Modal (optional) -->
            <div class="modal fade" id="quickViewModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                        <div class="modal-header border-0">
                            <h5 class="modal-title" id="quickViewTitle">Product</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <img id="quickViewImg" src="../assets/img/placeholder.svg" class="w-100 ma-rounded" alt="Product" />
                                </div>
                                <div class="col-md-6">
                                    <div class="ma-muted">Quick preview (UI only)</div>
                                    <div class="h4 mt-2 mb-3 ma-price" id="quickViewPrice">$0</div>
                                    <div class="d-flex gap-2">
                                        <a href="product.html" class="btn btn-ma">Open product</a>
                                        <a href="<?php echo isLoggedIn() ? 'cart' : 'login?redirect=cart'; ?>" class="btn btn-ma">Add to cart</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-ma-ghost" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php
    require_once('inc/footer.php');
    ?>

    <?php
    require_once('inc/bottom.php');
    ?>
    
</body>

</html>