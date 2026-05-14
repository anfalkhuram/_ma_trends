<?php
require_once('./assets/inc/admin_top.php');


?>

<body>
    <div class="ma-admin-shell d-flex flex-column flex-md-row ma-admin-collapsed">
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
                                <span class="badge badge-ma rounded-pill">Inactive: <?php echo getTotalByStatus($conn, 'categories', '0'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="ma-card p-3 h-100">
                            <div class="ma-muted small">Total products</div>
                            <div class="h4 fw-bold mb-1"><?php echo getTotal($conn, 'products'); ?></div>
                            <div>
                                <span class="badge  badge-trending rounded-pill">Active: <?php echo getTotalByStatus($conn, 'products', '1'); ?></span>
                                <span class="badge badge-ma rounded-pill">Inactive: <?php echo getTotalByStatus($conn, 'products', '0'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="ma-card p-3 h-100">
                            <div class="ma-muted small">Trending products</div>
                            <div class="h4 fw-bold mb-1"><?php echo getTotalByLabel($conn, 'product_details', '2'); ?></div>
                            <div class="badge badge-trending rounded-pill">🔥 Trending</div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="ma-card p-3 h-100">
                            <div class="ma-muted small">Orders</div>
                            <div class="h4 fw-bold mb-1"><?php echo getTotal($conn, 'orders'); ?></div>
                            <span class="badge  badge-trending rounded-pill">New: <?php echo getTotalByConfirmation($conn, 'orders', '0'); ?></span>
                            <span class=" badge badge-ma  rounded-pill">Pending: <?php echo getTotalByStatus($conn, 'orders', '0'); ?></span>
                            <span class="badge  bg-success rounded-pill">Delivered: <?php echo getTotalByStatus($conn, 'orders', '1'); ?></span>
                            <span class="badge  bg-danger rounded-pill">Canceled: <?php echo getTotalByStatus($conn, 'orders', '2'); ?></span>

                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="ma-card p-3 h-100">
                            <div class="ma-muted small">Users</div>
                            <div class="h4 fw-bold mb-1"><?php echo getTotal($conn, 'users'); ?></div>
                            <span class="badge  bg-success rounded-pill">Users: <?php echo getTotalByRole($conn, 'users', 'user'); ?></span>
                            <span class="badge  bg-warning rounded-pill">Admins: <?php echo getTotalByRole($conn, 'users', 'admin'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="ma-card p-3 p-md-4 mt-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div class="fw-bold">Recent Orders</div>
                        <a class="btn btn-ma-outline btn-sm" href="orders">View all</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-admin align-middle mb-0">
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
                                <?php
                                // Fetch orders placed today and yesterday
                                $today     = date('Y-m-d');
                                $yesterday = date('Y-m-d', strtotime('-1 day'));

                                $recentSql = "SELECT id, name, total, status, order_confirmation, created_at
                                              FROM orders
                                              WHERE DATE(created_at) IN ('$today', '$yesterday')
                                              ORDER BY created_at DESC
                                              LIMIT 20";
                                $recentRes = mysqli_query($conn, $recentSql);

                                if ($recentRes && mysqli_num_rows($recentRes) > 0):
                                    while ($ord = mysqli_fetch_assoc($recentRes)):
                                        // Status badge
                                        if ($ord['status'] == 1) {
                                            $sBadge = 'badge-trending'; $sText = 'Delivered';
                                        } elseif ($ord['status'] == 2) {
                                            $sBadge = 'bg-danger text-white'; $sText = 'Cancelled';
                                        } elseif ($ord['status'] == 0 && $ord['order_confirmation'] == 1) {
                                            $sBadge = 'bg-primary text-white'; $sText = 'Confirmed';
                                        } else {
                                            $sBadge = 'badge-ma'; $sText = 'Pending';
                                        }
                                        // Date label
                                        $orderDate = date('Y-m-d', strtotime($ord['created_at']));
                                        $dateLabel  = ($orderDate === $today) ? 'Today' : 'Yesterday';
                                ?>
                                    <tr>
                                        <td class="fw-semibold">ORD-<?php echo $ord['id']; ?></td>
                                        <td class="text-capitalize"><?php echo htmlspecialchars($ord['name']); ?></td>
                                        <td><span class="badge <?php echo $sBadge; ?> rounded-pill"><?php echo $sText; ?></span></td>
                                        <td>Rs. <?php echo number_format($ord['total'], 2); ?></td>
                                        <td class="ma-muted small"><?php echo $dateLabel; ?></td>
                                    </tr>
                                <?php
                                    endwhile;
                                else:
                                ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 ma-muted">
                                            No orders from today or yesterday.
                                        </td>
                                    </tr>
                                <?php endif; ?>
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