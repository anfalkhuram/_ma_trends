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
                    <div class="ma-muted">Update quantities and totals recalculates (front-end only).</div>
                </div>
                <a class="btn btn-ma-outline" href="shop.html">Continue shopping</a>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="ma-card p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="fw-bold">Products</div>
                            <div class="small ma-muted">2 items</div>
                        </div>

                        <!-- Item 1 -->
                        <div class="ma-card p-3 mb-3 js-cart-item" data-price="79">
                            <div class="row g-3 align-items-center">
                                <div class="col-4 col-md-3">
                                    <img src="../assets/img/placeholder.svg" class="w-100 ma-rounded" alt="Minimal Gold Watch" />
                                </div>
                                <div class="col-8 col-md-5">
                                    <div class="fw-semibold">Minimal Gold Watch</div>
                                    <div class="ma-muted small">Unisex • 40mm • Black leather</div>
                                    <div class="d-flex gap-2 mt-2">
                                        <span class="badge badge-trending rounded-pill">🔥 Trending</span>
                                        <a class="text-decoration-none ma-muted small" href="product.html">View details</a>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="js-qty-wrap d-flex align-items-center gap-2">
                                        <button class="btn btn-ma-outline btn-sm js-qty-minus" type="button">−</button>
                                        <input class="form-control form-control-sm js-qty text-center" value="1" />
                                        <button class="btn btn-ma-outline btn-sm js-qty-plus" type="button">+</button>
                                    </div>
                                </div>
                                <div class="col-md-2 text-md-end">
                                    <div class="ma-price js-line-total">$79.00</div>
                                    <a href="#" class="text-decoration-none small ma-muted js-remove-cart">Remove</a>
                                </div>
                            </div>
                        </div>

                        <!-- Item 2 -->
                        <div class="ma-card p-3 js-cart-item" data-price="39">
                            <div class="row g-3 align-items-center">
                                <div class="col-4 col-md-3">
                                    <img src="../assets/img/placeholder.svg" class="w-100 ma-rounded" alt="Matte Black Sunglasses" />
                                </div>
                                <div class="col-8 col-md-5">
                                    <div class="fw-semibold">Matte Black Sunglasses</div>
                                    <div class="ma-muted small">Men • UV400</div>
                                    <div class="d-flex gap-2 mt-2">
                                        <span class="badge badge-newdrop rounded-pill">New Drop</span>
                                        <a class="text-decoration-none ma-muted small" href="product.html">View details</a>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="js-qty-wrap d-flex align-items-center gap-2">
                                        <button class="btn btn-ma-outline btn-sm js-qty-minus" type="button">−</button>
                                        <input class="form-control form-control-sm js-qty text-center" value="1" />
                                        <button class="btn btn-ma-outline btn-sm js-qty-plus" type="button">+</button>
                                    </div>
                                </div>
                                <div class="col-md-2 text-md-end">
                                    <div class="ma-price js-line-total">$39.00</div>
                                    <a href="#" class="text-decoration-none small ma-muted js-remove-cart">Remove</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="ma-card p-3 p-md-4">
                        <div class="fw-bold mb-3">Order summary</div>
                        <div class="d-flex justify-content-between mb-2">
                            <div class="ma-muted">Subtotal</div>
                            <div class="js-cart-subtotal">$0.00</div>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <div class="ma-muted">Shipping</div>
                            <div class="js-cart-shipping" data-shipping="6.99">$6.99</div>
                        </div>
                        <hr class="border ma-border my-3" />
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-bold">Total</div>
                            <div class="h5 mb-0 ma-price js-cart-total">$0.00</div>
                        </div>
                        <a class="btn btn-ma w-100 mt-3" href="checkout.html">Proceed to checkout</a>
                        <div class="small ma-muted mt-2">Payment and order placement are UI only.</div>
                    </div>

                    <div class="ma-card p-3 p-md-4 mt-3">
                        <div class="fw-bold mb-2">Promo code</div>
                        <div class="input-group">
                            <input class="form-control" placeholder="Enter code" />
                            <button class="btn btn-ma-outline" type="button" data-bs-toggle="modal"
                                data-bs-target="#uiOnlyModal">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="uiOnlyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                <div class="modal-header border-0">
                    <h5 class="modal-title">UI only</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body ma-muted">This action is a front-end demo (connect backend later).</div>
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

</body>

</html>