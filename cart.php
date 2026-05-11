<?php

$pageTitle = "Your Cart | MATrends";

$description = "Review your selected products in your MATrends cart and proceed to secure checkout.";

$keywords = "MATrends cart, shopping cart, checkout MATrends";

$author = "MATrends";
$robots = "noindex, nofollow";

$ogTitle = "Your Cart | MATrends";
$ogDescription = "View your selected items and proceed to checkout at MATrends.";
$ogType = "website";
$ogUrl = "https://www.matrends.store/cart";
require_once("inc/top.php");

// Guard: must be logged in to view cart
requireLogin('cart');
?>

<body>
    <?php
    require_once("inc/navbar.php");
    ?>
    <main class="ma-hero pb-5">
        <div class="container">
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
                <div>
                    <div class="ma-kicker mb-2">Cart</div>
                    <h1 class="h2 fw-bold mb-1">Your items</h1>
                    <div class="ma-muted">Update quantities and totals recalculates</div>
                </div>
                <a class="btn btn-ma-outline" href="shop">Continue shopping</a>
            </div>

            <div class="row g-4" id="cartDataContainer">
                <?php include 'load_cart_data.php'; ?>
            </div>
        </div>
    </main>

    

    <!-- Remove Confirmation Modal -->
    <div class="modal fade" id="removeConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ma-card border-0 ma-shadow">
                <div class="modal-body p-4 p-md-5 text-center">
                    <div class="mb-4">
                        <i class="fas fa-trash-alt fa-3x text-danger"></i>
                    </div>
                    <h3 class="h4 fw-bold mb-3 text-white">Remove Item?</h3>
                    <p class="ma-muted mb-4">Are you sure you want to remove this item from your cart? This action cannot be undone.</p>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-ma-outline flex-grow-1" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger flex-grow-1" id="confirmRemoveBtn">Remove</button>
                    </div>
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

</body>

</html>