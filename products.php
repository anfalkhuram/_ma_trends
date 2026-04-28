<?php
require_once("inc/config.php");
$productId = isset($_GET['id']) ? $_GET['id'] : null;
if ($productId) {
    $sqlProduct = "SELECT * FROM products WHERE id = $productId";
    $resultProduct = mysqli_query($conn, $sqlProduct);
    if (mysqli_num_rows($resultProduct) > 0) {
        $rowProduct = mysqli_fetch_assoc($resultProduct);
    }
    $productDetailsSql = "SELECT * FROM product_details WHERE product_id = $productId";
    $productDetailsResult = mysqli_query($conn, $productDetailsSql);
    if (mysqli_num_rows($productDetailsResult) > 0) {
        $rowProductDetails = mysqli_fetch_assoc($productDetailsResult);
    }

    $productCategoryId = $rowProduct['category_id'];
    $sqlCategory = "SELECT * FROM categories WHERE id = $productCategoryId;";
    $resultCategory = mysqli_query($conn, $sqlCategory);
    $category = mysqli_fetch_assoc($resultCategory);
}else{
    // Redirect to shop page if no product ID is provided
    header("Location: shop");
    exit();
}

$productName = ucwords($rowProduct['name']);
$categoryName = ucwords($category['name']);

$pageTitle = $productName . " | MATrends";

$description = $productName . " available at MATrends. Shop premium quality " . $categoryName . " with modern design, affordable price and fast delivery.";

$keywords = $productName . ", " . $categoryName . ", MATrends, buy " . $categoryName . " online, trending accessories Pakistan";

$author = "MATrends";
$robots = "index, follow";


$ogTitle = $productName . " | MATrends";
$ogDescription = "Shop " . $productName . " at MATrends. Stylish " . $categoryName . " with modern design and affordable price.";
$ogType = "product";
$ogUrl = "https://www.matrends.store/products?id=" . $productId;


require_once('inc/top.php');


?>

<body>
    <?php
    require_once('inc/navbar.php');
    ?>

    <main class="ma-hero pb-5">
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../index.html" class="text-decoration-none ma-muted">Home</a></li>
                    <li class="breadcrumb-item"><a href="shop.html" class="text-decoration-none ma-muted">Shop</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page"><?php echo $rowProduct['name']; ?></li>
                </ol>
            </nav>

            <div class="row g-4">
                <!-- Gallery -->
                <div class="col-lg-6">
                    <div class="ma-card p-3">
                        <div class="position-relative">
                            <img class="w-100 ma-rounded js-main-img" src="./admins/assets/images/products/<?php echo $rowProductDetails['image']; ?>" alt="Product image" />
                            <div class="position-absolute top-0 start-0 p-3 d-flex gap-2">
                                <?php

                                if ($rowProductDetails['label'] == 1) {
                                    echo '<span class="badge badge-ma rounded-pill">Normal</span>';
                                } elseif ($rowProductDetails['label'] == 2) {
                                    echo '<span class="badge badge-trending rounded-pill">🔥 Trending</span>';
                                } else {
                                    echo '<span class="badge badge-ma rounded-pill">New Drop</span>';
                                }
                                ?>
                                <span class="badge badge-ma rounded-pill"><?php echo $rowProduct['discount']; ?>% off</span>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Details -->
                <div class="col-lg-6">
                    <div class="ma-card p-4 p-md-5">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div>
                                <h1 class="h2 fw-bold mb-2"><?php echo $rowProduct['name']; ?></h1>
                                <div class="ma-muted"><?php echo $rowProduct['properties']; ?></div>
                            </div>

                        </div>

                        <div class="d-flex align-items-baseline gap-2 mt-3">
                            <div class="h3 mb-0 ma-price">Rs.<?php echo $rowProduct['price']; ?></div>
                            <div class="ma-strike">Rs.<?php echo $rowProduct['old_price']; ?></div>
                            <span class="badge badge-ma rounded-pill">Save <?php echo $rowProduct['discount']; ?>%</span>
                        </div>

                        <p class="ma-muted mt-3 mb-4">
                            <?php
                            echo html_entity_decode($rowProduct['description']);
                            ?>
                        </p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label ma-muted text-capitalize"><?php echo $rowProductDetails['options']; ?></label>
                                <select class="form-select">
                                    <option value="<?php echo $rowProductDetails['value'] ?>;" selected><?php echo $rowProductDetails['value'] ?></option>
                                </select>
                            </div>

                        </div>

                        <div class="mt-4 d-flex flex-wrap gap-2 align-items-center">
                            <div class="js-qty-wrap d-flex align-items-center gap-2">
                                <button class="btn btn-ma-outline js-qty-minus" type="button" aria-label="Decrease">−</button>
                                <input class="form-control js-qty text-center" value="1" style="max-width: 90px;" />
                                <button class="btn btn-ma-outline js-qty-plus" type="button" aria-label="Increase">+</button>
                            </div>
                            <a class="btn btn-ma flex-grow-1 flex-md-grow-0" href="cart.html">Add to Cart</a>
                            <a class="btn btn-ma-outline flex-grow-1 flex-md-grow-0" href="cart.html">Buy Now</a>
                        </div>

                        <hr class="border ma-border my-4" />

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="ma-muted small">Delivery</div>
                                <div class="fw-semibold">Fast shipping</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="ma-muted small">Returns</div>
                                <div class="fw-semibold">Easy return policy</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews -->
            <section class="ma-section pb-0">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="ma-card p-4">
                            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                                <h2 class="h4 fw-bold mb-0">Reviews & Ratings</h2>
                                <span class="badge badge-ma rounded-pill"><?php echo $rowProductDetails['ratings']; ?> / 5</span>
                            </div>
                            <div class="d-flex flex-column gap-3">
                                <div class="ma-card p-3">
                                    <div class="d-flex justify-content-between">
                                        <div class="fw-semibold">Amina</div>
                                        <div class="ma-muted">★★★★★</div>
                                    </div>
                                    <div class="ma-muted small mt-1">Looks premium and feels comfortable. Perfect for daily outfits.</div>
                                </div>
                                <div class="ma-card p-3">
                                    <div class="d-flex justify-content-between">
                                        <div class="fw-semibold">Omar</div>
                                        <div class="ma-muted">★★★★☆</div>
                                    </div>
                                    <div class="ma-muted small mt-1">Great minimal style. Would love more strap options.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="ma-card p-4">
                            <h3 class="h5 fw-bold">Write a review</h3>
                            <div class="ma-muted small mb-3">Front-end UI only.</div>
                            <div class="mb-3">
                                <label class="form-label ma-muted">Name</label>
                                <input class="form-control" placeholder="Your name" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label ma-muted">Rating</label>
                                <select class="form-select">
                                    <option selected>5 stars</option>
                                    <option>4 stars</option>
                                    <option>3 stars</option>
                                    <option>2 stars</option>
                                    <option>1 star</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label ma-muted">Comment</label>
                                <textarea class="form-control" rows="4" placeholder="Share your experience..."></textarea>
                            </div>
                            <button class="btn btn-ma w-100" type="button" data-bs-toggle="modal"
                                data-bs-target="#uiOnlyModal">Submit</button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Related Trending Products -->
            <section class="ma-section">
                <div class="d-flex align-items-end justify-content-between gap-3 mb-3">
                    <div>
                        <h2 class="h4 fw-bold mb-1">Related Trending Products</h2>
                        <div class="ma-muted">More picks to match your look.</div>
                    </div>
                    <a class="btn btn-ma-ghost" href="shop.html#trending">Shop trending →</a>
                </div>
                <div class="row g-4">
                    <div class="col-6 col-md-4 col-xl-3">
                        <div class="ma-card ma-product">
                            <a class="ma-wishlist js-wishlist text-decoration-none" href="#" data-liked="false"><span
                                    data-icon>♡</span></a>
                            <img class="ma-card-img" src="../assets/img/placeholder.svg" alt="Matte Black Sunglasses" />
                            <div class="p-3">
                                <span class="badge badge-trending rounded-pill">🔥 Trending</span>
                                <div class="fw-semibold mt-2">Matte Black Sunglasses</div>
                                <div class="d-flex align-items-center justify-content-between mt-2">
                                    <div class="ma-price">$39</div>
                                    <a class="btn btn-sm btn-ma-outline" href="product.html">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-3">
                        <div class="ma-card ma-product">
                            <a class="ma-wishlist js-wishlist text-decoration-none" href="#" data-liked="false"><span
                                    data-icon>♡</span></a>
                            <img class="ma-card-img" src="../assets/img/placeholder.svg" alt="Minimal Ring Set" />
                            <div class="p-3">
                                <span class="badge badge-trending rounded-pill">🔥 Trending</span>
                                <div class="fw-semibold mt-2">Minimal Ring Set</div>
                                <div class="d-flex align-items-center justify-content-between mt-2">
                                    <div class="ma-price">$24</div>
                                    <a class="btn btn-sm btn-ma-outline" href="product.html">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-3">
                        <div class="ma-card ma-product">
                            <a class="ma-wishlist js-wishlist text-decoration-none" href="#" data-liked="false"><span
                                    data-icon>♡</span></a>
                            <img class="ma-card-img" src="../assets/img/placeholder.svg" alt="Beige Mini Bag" />
                            <div class="p-3">
                                <span class="badge badge-trending rounded-pill">🔥 Trending</span>
                                <div class="fw-semibold mt-2">Beige Mini Bag</div>
                                <div class="d-flex align-items-center justify-content-between mt-2">
                                    <div class="ma-price">$49</div>
                                    <a class="btn btn-sm btn-ma-outline" href="product.html">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-3">
                        <div class="ma-card ma-product">
                            <a class="ma-wishlist js-wishlist text-decoration-none" href="#" data-liked="false"><span
                                    data-icon>♡</span></a>
                            <img class="ma-card-img" src="../assets/img/placeholder.svg" alt="Couple Chain Set" />
                            <div class="p-3">
                                <span class="badge badge-trending rounded-pill">🔥 Trending</span>
                                <div class="fw-semibold mt-2">Couple Chain Set</div>
                                <div class="d-flex align-items-center justify-content-between mt-2">
                                    <div class="ma-price">$29</div>
                                    <a class="btn btn-sm btn-ma-outline" href="product.html">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>



    <!-- UI-only confirmation modal -->
    <div class="modal fade" id="uiOnlyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Saved (UI only)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body ma-muted">This is a front-end demo. Hook this to your backend later.</div>
                <div class="modal-footer border-0">
                    <button class="btn btn-ma" data-bs-dismiss="modal" type="button">OK</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    require_once('inc/footer.php');
    ?>

    <?php
    require_once('inc/bottom.php');
    ?>
    <script>
        document.getElementById("year").textContent = new Date().getFullYear();
    </script>
</body>

</html>