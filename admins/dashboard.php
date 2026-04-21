<?php
require_once('./assets/inc/admin_top.php');


?>

<body>
    <div class="ma-admin-shell d-flex flex-column flex-md-row">
        <!-- adminsidebar start -->
        <?php
        require_once('./assets/inc/admin_sidebar.php');
        ?>
        <!-- admin sidebar end -->


        <main class="flex-grow-1">
            <!-- Mobile admin menu (small screens) -->
            <?php
            require_once('./assets/inc/admin_sidebar_responsive.php');
            //  Mobile admin menu (small screens) end 
            
            // admin header start
            require_once('./assets/inc/admin_header.php');
            // admin header end
            ?>

            

            <div class="p-3 p-md-4">
                <div class="row g-3">
                    <div class="col-sm-6 col-lg-3">
                        <div class="ma-card p-3 h-100">
                            <div class="ma-muted small">Total Categories</div>
                            <div class="h4 fw-bold mb-1"><?php echo getTotal($conn, 'categories'); ?></div>
                            <div>
                                <span class="badge  badge-trending rounded-pill">Active: <?php echo getTotalByStatus($conn, 'categories', '1'); ?></span>
                                <span class="badge badge-ma rounded-pill">Inactive:  <?php echo getTotalByStatus($conn, 'categories', '0'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="ma-card p-3 h-100">
                            <div class="ma-muted small">Total products</div>
                            <div class="h4 fw-bold mb-1">248</div>
                            <div class="badge badge-trending rounded-pill">+8 new</div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="ma-card p-3 h-100">
                            <div class="ma-muted small">Trending products</div>
                            <div class="h4 fw-bold mb-1">34</div>
                            <div class="badge badge-trending rounded-pill">🔥 Trending</div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="ma-card p-3 h-100">
                            <div class="ma-muted small">Orders</div>
                            <div class="h4 fw-bold mb-1">1,284</div>
                            <div class="ma-muted small">UI table ready</div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="ma-card p-3 h-100">
                            <div class="ma-muted small">Users</div>
                            <div class="h4 fw-bold mb-1">5,932</div>
                            <div class="ma-muted small">UI table ready</div>
                        </div>
                    </div>
                </div>

                <div class="ma-card p-3 p-md-4 mt-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div class="fw-bold">Recent orders (UI)</div>
                        <a class="btn btn-ma-outline btn-sm" href="orders.html">View all</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#1824</td>
                                    <td>Alex Doe</td>
                                    <td><span class="badge badge-trending rounded-pill">Paid</span></td>
                                    <td>$138.00</td>
                                    <td>Today</td>
                                </tr>
                                <tr>
                                    <td>#1823</td>
                                    <td>Amina K.</td>
                                    <td><span class="badge badge-newdrop rounded-pill">Pending</span></td>
                                    <td>$79.00</td>
                                    <td>Today</td>
                                </tr>
                                <tr>
                                    <td>#1822</td>
                                    <td>Omar F.</td>
                                    <td><span class="badge badge-ma rounded-pill">Shipped</span></td>
                                    <td>$64.00</td>
                                    <td>Yesterday</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php
    require_once('./assets/inc/admin_bottom.php');
    ?>
</body>

</html>