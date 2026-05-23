<?php
require_once("inc/config.php");
$productId = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Session helpers for the review section
$isUserLoggedIn = isset($_SESSION['user']['id']) && !empty($_SESSION['user']['id']);
$loggedUserName = $isUserLoggedIn ? htmlspecialchars($_SESSION['user']['name']) : '';
$loggedUserId   = $isUserLoggedIn ? (int)$_SESSION['user']['id'] : 0;
if ($productId) {
    // JOIN categories to check category status as well
    $sqlProduct = "SELECT p.*, c.status as cat_status, c.name as cat_name 
                   FROM products p 
                   JOIN categories c ON p.category_id = c.id 
                   WHERE p.id = $productId";
    $resultProduct = mysqli_query($conn, $sqlProduct);

    if (mysqli_num_rows($resultProduct) > 0) {
        $rowProduct = mysqli_fetch_assoc($resultProduct);

        // Redirect if product is hidden OR its category is hidden
        if ($rowProduct['status'] == 0 || $rowProduct['cat_status'] == 0) {
            header("Location: shop");
            exit();
        }

        // Populate category array for compatibility
        $category = ['name' => $rowProduct['cat_name'], 'status' => $rowProduct['cat_status']];
    } else {
        // Product not found in database
        header("Location: shop");
        exit();
    }

    $productDetailsSql = "SELECT * FROM product_details WHERE product_id = $productId";
    $productDetailsResult = mysqli_query($conn, $productDetailsSql);
    if ($productDetailsResult && mysqli_num_rows($productDetailsResult) > 0) {
        $rowProductDetails = mysqli_fetch_assoc($productDetailsResult);
    } else {
        // If product has no details, it shouldn't be accessible
        header("Location: shop");
        exit();
    }
} else {
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
                                <div class="ma-rating mt-2">
                                    <?php
                                    $rating = floor($rowProductDetails['ratings']);

                                    // Filled stars
                                    for ($i = 0; $i < $rating; $i++) {
                                        echo '<i class="fas fa-star"></i>';
                                    }

                                    // Empty stars
                                    for ($i = $rating; $i < 5; $i++) {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                    ?>


                                </div>
                            </div>

                        </div>

                        <div class="d-flex align-items-baseline gap-2 mt-3">
                            <div class="h3 mb-0 ma-price">Rs.<?php echo $rowProduct['price']; ?></div>
                            <div class="ma-strike">Rs.<?php echo $rowProduct['old_price']; ?></div>
                            <span class="badge badge-ma rounded-pill">Save <?php echo $rowProduct['discount']; ?>%</span>
                        </div>

                        <div class="mt-3 mb-4">
                            <div class="ma-muted" id="product-description" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?php
                                echo html_entity_decode($rowProduct['description']);
                                ?>
                            </div>
                            <a href="javascript:void(0)" id="read-more-btn" class="text-decoration-none small fw-bold mt-1" style="display: none; color: #bda379;">...Read more</a>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label ma-muted text-capitalize"><?php echo $rowProductDetails['options']; ?></label>
                                <select class="form-select">
                                    <option value="<?php echo $rowProductDetails['value'] ?>;" selected><?php echo $rowProductDetails['value'] ?></option>
                                </select>
                            </div>

                        </div>

                        <div class="mt-4 d-flex flex-wrap justify-content-between align-items-center">
                            <div class="js-qty-wrap d-flex align-items-center gap-2">
                                <button class="btn btn-ma-outline js-qty-minus" type="button" aria-label="Decrease">−</button>
                                <input class="form-control js-qty text-center" value="1" style="max-width: 90px;" />
                                <button class="btn btn-ma-outline js-qty-plus" type="button" aria-label="Increase">+</button>
                            </div>
                            <button class="btn btn-ma flex-grow-1 flex-md-grow-0 mt-2 mt-md-0 mt-lg-0 js-add-to-cart" data-product-id="<?php echo $productId; ?>">Add to Cart</button>
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
            <?php
            // Fetch approved reviews
            $sqlReviews = "SELECT * FROM product_feedback WHERE product_id = $productId AND status = 1 ORDER BY created_at DESC";
            $resReviews = mysqli_query($conn, $sqlReviews);
            $reviews    = [];
            if ($resReviews && mysqli_num_rows($resReviews) > 0) {
                while ($r = mysqli_fetch_assoc($resReviews)) {
                    $reviews[] = $r;
                }
            }
            // Average from approved reviews
            $avgRes = mysqli_query($conn, "SELECT ROUND(AVG(rating),1) AS avg FROM product_feedback WHERE product_id = $productId AND status = 1");
            $avgRow = mysqli_fetch_assoc($avgRes);
            $avgRating = $avgRow['avg'] ?? 0;

            // Check if current user already reviewed
            $alreadyReviewed = false;
            if ($isUserLoggedIn) {
                $chkRes = mysqli_query($conn, "SELECT id FROM product_feedback WHERE user_id = $loggedUserId AND product_id = $productId LIMIT 1");
                $alreadyReviewed = $chkRes && mysqli_num_rows($chkRes) > 0;
            }
            ?>
            <section class="ma-section pb-0">
                <div class="row g-4">
                    <!-- Reviews List -->
                    <div class="col-lg-7">
                        <div class="ma-card p-4">
                            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                                <h2 class="h4 fw-bold mb-0">Reviews &amp; Ratings</h2>
                                <?php if ($avgRating > 0): ?>
                                    <span class="badge badge-ma rounded-pill"><?php echo $avgRating; ?> / 5</span>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex flex-column gap-3" id="js-reviews-list">
                                <?php if (!empty($reviews)): ?>
                                    <?php foreach ($reviews as $rev): ?>
                                        <?php
                                        $stars = '';
                                        for ($s = 1; $s <= 5; $s++) {
                                            $stars .= $s <= $rev['rating'] ? '★' : '☆';
                                        }
                                        ?>
                                        <div class="ma-card p-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="fw-semibold text-capitalize"><?php echo htmlspecialchars($rev['name']); ?></div>
                                                <div class="ma-muted" style="letter-spacing:2px;"><?php echo $stars; ?></div>
                                            </div>
                                            <div class="ma-muted small mt-1"><?php echo htmlspecialchars($rev['feedback']); ?></div>
                                            <div class="ma-muted" style="font-size:0.72rem; margin-top:4px;"><?php echo date('M d, Y', strtotime($rev['created_at'])); ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="ma-muted small text-center py-3" id="js-no-reviews-msg">No reviews yet. Be the first to review this product!</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Review Form / Guest Prompt -->
                    <div class="col-lg-5">
                        <div class="ma-card p-4">
                            <?php if ($isUserLoggedIn && !$alreadyReviewed): ?>
                                <!-- Logged-in: show form -->
                                <h3 class="h5 fw-bold">Write a Review</h3>
                                <form id="js-review-form" novalidate>
                                    <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                    <div class="mb-3">
                                        <label class="form-label ma-muted">Name</label>
                                        <input class="form-control" name="name" value="<?php echo $loggedUserName; ?>" readonly style="opacity:.7; cursor:not-allowed;" />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label ma-muted">Rating</label>
                                        <select class="form-select" name="rating" required>
                                            <option value="5" selected>★★★★★ &nbsp;5 Stars</option>
                                            <option value="4">★★★★☆ &nbsp;4 Stars</option>
                                            <option value="3">★★★☆☆ &nbsp;3 Stars</option>
                                            <option value="2">★★☆☆☆ &nbsp;2 Stars</option>
                                            <option value="1">★☆☆☆☆ &nbsp;1 Star</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label ma-muted">Comment</label>
                                        <textarea class="form-control" name="feedback" rows="4" placeholder="Share your experience..." required></textarea>
                                    </div>
                                    <button class="btn btn-ma w-100" type="submit" id="js-review-submit-btn">Submit Review</button>
                                </form>
                            <?php elseif ($isUserLoggedIn && $alreadyReviewed): ?>
                                <!-- Already reviewed -->
                                <div class="text-center py-3">
                                    <div style="font-size:2.5rem;">✅</div>
                                    <div class="fw-semibold mt-2">You've already reviewed this product.</div>
                                    <div class="ma-muted small mt-1">Your review is pending approval and will appear shortly.</div>
                                </div>
                            <?php else: ?>
                                <!-- Guest: show login prompt -->
                                <div class="text-center py-3">
                                    <div style="font-size:2.5rem;">🔒</div>
                                    <h3 class="h5 fw-bold mt-3">Login to Write a Review</h3>
                                    <p class="ma-muted small">Share your experience with other shoppers by logging into your account.</p>
                                    <a href="login?redirect=products%3Fid%3D<?php echo $productId; ?>" class="btn btn-ma w-100">Login to Review</a>
                                </div>
                            <?php endif; ?>
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
                    <a class="btn btn-ma-ghost" href="shop">Shop trending →</a>
                </div>
                <div class="row g-4">


                    <?php
                    $sqlProducts = "SELECT p.* FROM products p JOIN product_details pd ON pd.product_id = p.id WHERE p.status = 1 AND pd.label = 2 LIMIT 4;";
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
                            <div class="col-12 col-md-4 col-xl-3">
                                <div class="ma-card ma-product">

                                    <img class="ma-card-img" src="./admins/assets/images/products/<?php echo $productDetails['image']; ?>" alt="<?php echo $rowProduct['name']; ?>" />
                                    <div class="p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">

                                            <?php

                                            if ($productDetails['label'] == 1) {
                                                echo '<span class="badge badge-ma rounded-pill">Normal</span>';
                                            } elseif ($productDetails['label'] == 2) {
                                                echo '<span class="badge badge-trending rounded-pill">🔥 Trending</span>';
                                            } else {
                                                echo '<span class="badge badge-ma rounded-pill">New Drop</span>';
                                            }
                                            ?>


                                            <span class="small ma-muted text-capitalize"><?php echo $category['name']; ?></span>
                                        </div>
                                        <div class="fw-semibold"><?php echo $rowProduct['name']; ?></div>
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
                        echo '<div class="col-12"><p class="text-center ma-muted">No trending products available at the moment.</p></div>';
                    }
                    ?>

                </div>
            </section>
        </div>
    </main>



    <!-- Review Response Modal -->
    <div class="modal fade" id="reviewResponseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white" id="reviewModalTitle">Review Submitted</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body ma-muted" id="reviewModalBody"></div>
                <div class="modal-footer border-0">
                    <button class="btn btn-ma" data-bs-dismiss="modal" type="button">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function() {
        // Description Read More Logic
        const desc = document.getElementById('product-description');
        const readMoreBtn = document.getElementById('read-more-btn');
        if (desc && readMoreBtn) {
            // Check if text overflows
            if (desc.scrollHeight > desc.clientHeight) {
                readMoreBtn.style.display = 'inline-block';
            }

            readMoreBtn.addEventListener('click', function() {
                if (desc.style.webkitLineClamp === '2') {
                    desc.style.webkitLineClamp = 'unset';
                    readMoreBtn.innerText = 'Show less';
                } else {
                    desc.style.webkitLineClamp = '2';
                    readMoreBtn.innerText = '...Read more';
                }
            });
        }

        const reviewForm = document.getElementById('js-review-form');
        if (!reviewForm) return;

        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('js-review-submit-btn');
            const originalText = submitBtn.innerText;
            submitBtn.innerText = 'Submitting...';
            submitBtn.disabled = true;

            const formData = new FormData(reviewForm);

            fetch('handlers/submit_review.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.innerText = originalText;
                submitBtn.disabled = false;

                document.getElementById('reviewModalTitle').textContent = data.success ? '🎉 Review Submitted' : '⚠️ Could Not Submit';
                document.getElementById('reviewModalBody').textContent  = data.message;

                const modal = new bootstrap.Modal(document.getElementById('reviewResponseModal'));
                modal.show();

                if (data.success) {
                    reviewForm.reset();
                    // Disable submit to prevent double submission
                    submitBtn.innerText  = 'Review Submitted ✓';
                    submitBtn.disabled   = true;

                    // Remove "no reviews" placeholder if present
                    const noMsg = document.getElementById('js-no-reviews-msg');
                    if (noMsg) noMsg.remove();
                }
            })
            .catch(() => {
                submitBtn.innerText = originalText;
                submitBtn.disabled  = false;
                document.getElementById('reviewModalTitle').textContent = '⚠️ Network Error';
                document.getElementById('reviewModalBody').textContent  = 'A network error occurred. Please try again.';
                const modal = new bootstrap.Modal(document.getElementById('reviewResponseModal'));
                modal.show();
            });
        });
    })();
    </script>

    <?php
    require_once('inc/footer.php');
    ?>

    <?php
    require_once('inc/bottom.php');
    ?>

</body>

</html>