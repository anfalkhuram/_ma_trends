<?php
$pageTitle = "MATrends – Trending Rings, Watches, Bags & Fashion Accessories Online";
$description = "Shop trending rings, watches, bags, and fashion accessories at MATrends. Discover couple collections, stylish jewelry, and everyday essentials with affordable prices and modern designs.";
$keywords = "MATrends, trending rings, fashion rings, watches for men women, jewelry Pakistan, bags online, couple rings, accessories store, trendy jewelry, fashion accessories Pakistan";
$author = "MATrends";
$robots = "index, follow";

$ogTitle = "MATrends – Trending Rings, Watches & Accessories";
$ogDescription = "Discover trending rings, watches, bags and couple collections at MATrends. Stylish accessories at affordable prices.";
$ogType = "website";
$ogUrl = "https://www.matrends.store/";



require_once('inc/top.php');
?>

<body>
    <!-- Navbar -->
    <?php
    require_once('inc/navbar.php');
    ?>

    <!-- Hero -->
    <header class="ma-hero">
        <div class="container">
            <div class="ma-hero-card ma-shadow">
                <!-- Shimmer accent line -->
                <div class="ma-hero-shimmer"></div>
                <div class="row g-0 align-items-stretch">
                    <div class="col-lg-6 ma-hero-img-col">
                        <div class="ma-hero-art h-100">
                            <picture>
                                <source media="(max-width: 991px)" srcset="assets/img/hero-image-small.webp">
                                <img src="assets/img/hero-image.webp" alt="MA Trends — Premium Accessories Collection" class="ma-hero-img" fetchpriority="high" decoding="async">
                            </picture>
                            <!-- Luxury overlay -->
                            <div class="ma-hero-img-overlay"></div>
                            <!-- Floating brand watermark on image -->
                            <div class="ma-hero-watermark">MA</div>
                        </div>
                    </div>
                    <div class="col-lg-6 d-flex align-items-center">
                        <div class="ma-hero-content">
                            <div class="ma-kicker mb-3">
                                <span class="ma-kicker-diamond">◆</span> MA Trends
                            </div>
                            <h1 class="ma-hero-headline">Discover What's<br><span class="ma-gold-text">Trending Now</span></h1>
                            <p class="lead ma-muted mb-4">
                                Premium looks, everyday prices. Curated accessories for men, women, and unisex style.
                            </p>
                            <div class="d-flex flex-wrap gap-3">
                                <a class="btn btn-ma btn-ma-hero" href="shop">
                                    <span>Shop Trending</span>
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                                <a class="btn btn-ma-outline btn-ma-hero" href="shop">Explore All</a>
                            </div>
                            <div class="ma-hero-badges">
                                <span class="badge badge-trending rounded-pill px-3 py-2">
                                    <i class="fas fa-fire-alt me-1"></i>Trending highlights
                                </span>
                                <span class="badge badge-newdrop rounded-pill px-3 py-2">
                                    <i class="fas fa-bolt me-1"></i>New drops weekly
                                </span>
                                <span class="badge badge-ma rounded-pill px-3 py-2">
                                    <i class="fas fa-shipping-fast me-1"></i>Fast delivery
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Azadi Sale Countdown -->
    <section class="ma-section pb-0" id="azadi-sale">
        <div class="container">
            <div class="ma-hero-card ma-shadow p-4 p-md-5 text-center" style="background: linear-gradient(135deg, rgba(17, 19, 25, .95), rgba(215, 180, 106, .15)); border: 1px solid var(--ma-gold); margin-bottom: 30px;">
                <div class="ma-hero-shimmer"></div>
                <div class="ma-kicker mb-3 justify-content-center">
                    <span class="ma-kicker-diamond">◆</span> Independence Special
                </div>
                <h2 class="ma-hero-headline mb-2">Azadi Sale <span class="ma-gold-text">Is Here!</span></h2>
                <p class="lead ma-muted mb-4">Celebrate freedom with 100% Free Delivery on all orders for the entire month of August.</p>
                
                <div class="d-flex justify-content-center align-items-center gap-3 gap-md-4 mb-4" id="countdown-timer">
                    <div class="text-center">
                        <div class="display-5 fw-bold mb-0 ma-gold-text" id="cd-days">00</div>
                        <div class="small ma-muted text-uppercase" style="letter-spacing: 1px;">Days</div>
                    </div>
                    <div class="display-6 fw-bold ma-muted pb-3">:</div>
                    <div class="text-center">
                        <div class="display-5 fw-bold mb-0 ma-gold-text" id="cd-hours">00</div>
                        <div class="small ma-muted text-uppercase" style="letter-spacing: 1px;">Hours</div>
                    </div>
                    <div class="display-6 fw-bold ma-muted pb-3">:</div>
                    <div class="text-center">
                        <div class="display-5 fw-bold mb-0 ma-gold-text" id="cd-minutes">00</div>
                        <div class="small ma-muted text-uppercase" style="letter-spacing: 1px;">Mins</div>
                    </div>
                    <div class="display-6 fw-bold ma-muted pb-3">:</div>
                    <div class="text-center">
                        <div class="display-5 fw-bold mb-0 ma-gold-text" id="cd-seconds">00</div>
                        <div class="small ma-muted text-uppercase" style="letter-spacing: 1px;">Secs</div>
                    </div>
                </div>

                <a class="btn btn-ma btn-ma-hero mt-2" href="shop">
                    <span>Shop the Sale</span>
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Trending This Week -->
    <section class="ma-section pt-0" id="trending">
        <div class="container">
            <div class="d-flex align-items-end justify-content-between gap-3 mb-3">
                <div>
                    <h2 class="h3 fw-bold mb-1">Trending This Week</h2>
                    <div class="ma-muted">Hot picks people are saving right now.</div>
                </div>
                <a class="btn btn-ma-ghost" href="shop">Shop more -></a>
            </div>

            <div class="row g-4">
                <!-- 8 cards -->

                <?php
                $sqlProducts = "SELECT p.*, pd.label, pd.image, c.name as category_name 
                                FROM products p 
                                JOIN product_details pd ON pd.product_id = p.id 
                                JOIN categories c ON p.category_id = c.id 
                                WHERE p.status = 1 AND pd.label = 2;";
                $resultProducts = mysqli_query($conn, $sqlProducts);
                if (mysqli_num_rows($resultProducts) > 0) {
                    while ($rowProduct = mysqli_fetch_assoc($resultProducts)) {
                        $productDetails = [
                            'label' => $rowProduct['label'],
                            'image' => $rowProduct['image']
                        ];
                        $category = [
                            'name' => $rowProduct['category_name']
                        ];
                ?>
                        <div class="col-12 col-md-4 col-xl-3">
                            <div class="ma-card ma-product">

                                <img class="ma-card-img" src="./admins/assets/images/products/<?php echo $productDetails['image']; ?>" alt="<?php echo $rowProduct['name']; ?>" loading="lazy" />
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
                }else {
                    echo '<div class="col-12"><p class="text-center ma-muted">No trending products available at the moment.</p></div>';
                }
                ?>
                

            </div>
        </div>
    </section>

    <!-- Shop by Category -->
    <section class="ma-section">
        <div class="container">
            <div class="d-flex align-items-end justify-content-between gap-3 mb-3">
                <div>
                    <h2 class="h3 fw-bold mb-1">Shop by Category</h2>
                    <div class="ma-muted">Find your vibe in seconds.</div>
                </div>
            </div>
            <div class="row g-3">
                <?php
                $sqlCategories = "SELECT * FROM categories where status = 1";
                $resultCategories = mysqli_query($conn, $sqlCategories);
                if (mysqli_num_rows($resultCategories) > 0) {
                    while ($rowCategory = mysqli_fetch_assoc($resultCategories)) {
                ?>
                        <div class="col-6 col-lg-3">
                            <a class="ma-card p-4 d-block text-decoration-none" href=".<?php echo $rowCategory['slug']; ?>?id=<?php echo $rowCategory['id'];?>">
                                <div class="fw-bold mb-1 text-capitalize"><?php echo $rowCategory['name']; ?></div>
                            </a>
                        </div>

                <?php
                    }
                }

                ?>
            </div>
        </div>
    </section>

    <!-- Couple Collection -->
    <section class="ma-section">
        <div class="container">
            <div class="ma-couple-card ma-shadow">
                <div class="ma-couple-shimmer"></div>
                <div class="row g-0 align-items-stretch">
                    <!-- Image Column (Right on desktop, Top on mobile) -->
                    <div class="col-lg-6 order-lg-2 ma-couple-img-col">
                        <div class="ma-couple-art h-100">
                            <picture>
                                <source media="(max-width: 991px)" srcset="assets/img/couple-collection-small.webp">
                                <img src="assets/img/couple-collection.webp" alt="Couple Collection" class="ma-couple-img" loading="lazy">
                            </picture>
                            <div class="ma-couple-img-overlay"></div>
                        </div>
                    </div>
                    <!-- Content Column (Left on desktop, Bottom on mobile) -->
                    <div class="col-lg-6 order-lg-1 d-flex align-items-center">
                        <div class="ma-couple-content">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <div class="ma-kicker">
                                    <span class="ma-kicker-diamond">◆</span> Couple Collection
                                </div>
                                <span class="badge badge-trending rounded-pill px-3 py-2"><i class="fas fa-fire-alt me-1"></i>Trending highlight</span>
                            </div>
                            <h2 class="ma-couple-headline mb-3">Match energy,<br><span class="ma-gold-text">not just outfits.</span></h2>
                            <p class="lead ma-muted mb-4">Coordinated accessories designed to feel personal, modern, and effortless.</p>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="ma-couple-inner-card p-3 h-100">
                                        <div class="fw-semibold text-white">“His & Hers” Chains</div>
                                        <div class="ma-muted small">Minimal metal, premium feel</div>
                                        <div class="mt-3 d-flex gap-2">
                                            <span class="badge badge-ma rounded-pill">Unisex</span>
                                            <span class="badge badge-trending rounded-pill">🔥</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ma-couple-inner-card p-3 h-100">
                                        <div class="fw-semibold text-white">Matched Watch Set</div>
                                        <div class="ma-muted small">Clean dial + comfort strap</div>
                                        <div class="mt-3 d-flex gap-2">
                                            <span class="badge badge-ma rounded-pill">Couple</span>
                                            <span class="badge badge-newdrop rounded-pill">New Drop</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <a class="btn btn-ma btn-ma-hero" href="category-couple-collection">
                                    <span>Explore Couple Collection</span>
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why MA Trends -->
    <section class="ma-section">
        <div class="container">
            <div class="d-flex align-items-end justify-content-between gap-3 mb-3">
                <div>
                    <h2 class="h3 fw-bold mb-1">Why MA Trends?</h2>
                    <div class="ma-muted">Small details that make shopping feel premium.</div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6 col-xl-3">
                    <div class="ma-card p-4 h-100">
                        <div class="fw-bold mb-2">Trend-driven</div>
                        <div class="ma-muted">Curated drops built around what’s hot right now.</div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="ma-card p-4 h-100">
                        <div class="fw-bold mb-2">Quality checked</div>
                        <div class="ma-muted">Materials + finishing inspected for daily wear.</div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="ma-card p-4 h-100">
                        <div class="fw-bold mb-2">Fast delivery</div>
                        <div class="ma-muted">Quick shipping with a clean structure.</div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="ma-card p-4 h-100">
                        <div class="fw-bold mb-2">Easy returns</div>
                        <div class="ma-muted">Simple return flow designed for clarity.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Footer -->

    <?php
    require_once('inc/footer.php');
    ?>

    <?php
    require_once('inc/bottom.php');
    ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Target is August 31, end of the day
            const currentYear = new Date().getFullYear();
            // Since it's for the full month of August, set target to Aug 31 of current year
            const targetDate = new Date(`August 31, ${currentYear} 23:59:59`).getTime();

            const timer = setInterval(function() {
                const now = new Date().getTime();
                const distance = targetDate - now;

                if (distance < 0) {
                    clearInterval(timer);
                    const cdEl = document.getElementById("countdown-timer");
                    if (cdEl) {
                        cdEl.innerHTML = "<div class='h4 fw-bold ma-gold-text'>The Azadi Sale Has Ended!</div>";
                    }
                    return;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                const elDays = document.getElementById("cd-days");
                const elHours = document.getElementById("cd-hours");
                const elMinutes = document.getElementById("cd-minutes");
                const elSeconds = document.getElementById("cd-seconds");

                if (elDays) elDays.innerText = days.toString().padStart(2, '0');
                if (elHours) elHours.innerText = hours.toString().padStart(2, '0');
                if (elMinutes) elMinutes.innerText = minutes.toString().padStart(2, '0');
                if (elSeconds) elSeconds.innerText = seconds.toString().padStart(2, '0');
            }, 1000);
        });
    </script>
</body>

</html>