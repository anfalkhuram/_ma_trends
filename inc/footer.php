<footer class="ma-footer pt-5 pb-4">
    <div class="container">
        <div class="row g-4 mb-5">
            <!-- Brand & Contact -->
            <div class="col-lg-4">
                <a class="navbar-brand d-flex align-items-center gap-2 mb-3" href="index">
                    <span class="ma-pill color-soft-gold fs-4"><img src="assets/img/ma_trends_ill.png" alt="logo" width="35"> <span class="h5 mb-0">Trends</span></span>
                </a>
                <p class="ma-muted small mb-4" style="max-width: 300px;">
                    Curating the latest trends in fashion accessories. Premium looks, everyday prices, and fast delivery.
                </p>
                <div class="d-flex flex-column gap-2">
                    <a href="mailto:team@matrends.store" class="text-decoration-none ma-muted small ma-hover-gold">
                        <i class="fas fa-envelope me-2 color-gold"></i> team@matrends.store
                    </a>
                    <a href="tel:+923171417715" class="text-decoration-none ma-muted small ma-hover-gold">
                        <i class="fas fa-phone-alt me-2 color-gold"></i> +92 309 7410140
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-6 col-lg-2">
                <h6 class="fw-bold mb-3 text-white">Quick Links</h6>
                <ul class="list-unstyled d-flex flex-column gap-2">
                    <li><a href="index" class="text-decoration-none ma-muted small ma-hover-gold">Home</a></li>
                    <li><a href="shop" class="text-decoration-none ma-muted small ma-hover-gold">Shop</a></li>
                    <li><a href="cart" class="text-decoration-none ma-muted small ma-hover-gold">Cart</a></li>
                </ul>
            </div>

            <!-- Categories -->
            <div class="col-6 col-lg-3">
                <h6 class="fw-bold mb-3 text-white">Categories</h6>
                <ul class="list-unstyled d-flex flex-column gap-2">
                    <?php 
                    // Re-fetching categories for footer
                    $sqlFootCat = "SELECT * FROM categories WHERE status = 1 LIMIT 6";
                    $resFootCat = mysqli_query($conn, $sqlFootCat);
                    if ($resFootCat && mysqli_num_rows($resFootCat) > 0) {
                        while($fCat = mysqli_fetch_assoc($resFootCat)) {
                            echo '<li><a href=".'.$fCat['slug'].'?id='.$fCat['id'].'" class="text-decoration-none ma-muted small text-capitalize ma-hover-gold">'.$fCat['name'].'</a></li>';
                        }
                    }
                    ?>
                </ul>
            </div>

            <!-- Newsletter/Social -->
            <div class="col-lg-3">
                <h6 class="fw-bold mb-3 text-white">Follow Us</h6>
                <div class="d-flex gap-3 mb-4">
                    <a href="#" class="btn btn-sm btn-ma-outline rounded-circle" style="width: 36px; height: 36px; padding: 6px;"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="btn btn-sm btn-ma-outline rounded-circle" style="width: 36px; height: 36px; padding: 6px;"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-sm btn-ma-outline rounded-circle" style="width: 36px; height: 36px; padding: 6px;"><i class="fab fa-tiktok"></i></a>
                </div>
                
            </div>
        </div>

        <hr class="border ma-border my-4" />

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="ma-muted small">
                &copy; <?php echo date('Y'); ?> MA Trends. Developed by 
                <a href="https://cybersamuraisolutions.com" target="_blank" class="text-decoration-none color-gold fw-semibold">CyberSamurai Software Solutions</a>
            </div>
          
        </div>
    </div>
</footer>
