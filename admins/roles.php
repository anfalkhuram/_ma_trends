<?php
require_once('./assets/inc/admin_top.php');
?>

<body>
    <div class="ma-admin-shell d-flex flex-column flex-md-row ma-admin-collapsed">
        <?php require_once('./assets/inc/admin_sidebar.php'); ?>

        <main class="flex-grow-1" style="min-width: 0;">
            <!-- Mobile admin menu (small screens) -->
            <?php require_once('./assets/inc/admin_sidebar_responsive.php'); ?>

            <!-- admin header start -->
            <?php require_once('./assets/inc/admin_header.php'); ?>

            <div class="p-3 mt-3">
                <div class="ma-admin-table-wrap ma-card p-3 p-md-4 w-100" style="max-width: 100%; overflow: hidden;">
                    <!-- Header -->
                    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-3 mb-3">
                        <div class="d-flex align-items-center gap-5">
                            <div class="d-flex align-items-center gap-2">
                                <label class="ma-muted small mb-0 text-nowrap">Show</label>
                                <select class="form-select form-select-sm js-admin-entries" style="width: auto; min-width: 70px;">
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span class="ma-muted small text-nowrap">entries</span>
                            </div>
                            <div>
                                <label class="ma-muted small mb-0 text-nowrap fw-bold">Sort</label>
                                <span class="ma-sort-controls" data-sort="id">
                                    <i class="fa-solid fa-arrow-up ma-sort-arrow small" data-dir="asc"></i>
                                    <i class="fa-solid fa-arrow-down ma-sort-arrow small active" data-dir="desc"></i>
                                </span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <input type="search" class="form-control form-control-sm js-admin-search" placeholder="Search…" style="max-width: 220px;" />
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-admin align-middle mb-0" id="myTable">
                            <thead>
                                <tr>
                                    <th>#Sr</th>
                                    <th>User ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                </tr>
                            </thead>
                            <tbody class="js-admin-tbody">
                                <?php
                                $sqlRoles = "SELECT * FROM users WHERE status != 'user' ORDER BY id DESC";
                                $resRoles = mysqli_query($conn, $sqlRoles);
                                if ($resRoles && mysqli_num_rows($resRoles) > 0) {
                                    $sr = 0;
                                    while ($row = mysqli_fetch_assoc($resRoles)) {
                                        $sr++;
                                        $searchString = $row['name'] . ' ' . $row['email'] . ' ' . ($row['phone'] ?? '') . ' ' . $row['status'];
                                        
                                        $roleBadge = '<span class="badge badge-ma rounded-pill px-3">' . htmlspecialchars(ucfirst($row['status'])) . '</span>';
                                        if (strtolower($row['status']) === 'admin') {
                                            $roleBadge = '<span class="badge badge-trending rounded-pill px-3">Admin</span>';
                                        }
                                ?>
                                    <tr class="js-admin-row" data-sr="<?php echo $sr; ?>" data-id="<?php echo $row['id']; ?>" data-name="<?php echo htmlspecialchars($searchString); ?>">
                                        <td><?php echo $sr; ?></td>
                                        <td>#<?php echo $row['id']; ?></td>
                                        <td class="text-capitalize"><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['phone'] ?? '—'); ?></td>
                                        <td><?php echo $roleBadge; ?></td>
                                    </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                    <tr>
                                        <td colspan="6" class="text-center ma-muted py-4">No staff or admin accounts found.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 mt-3">
                        <div class="ma-muted small js-admin-footer-info">Showing 0 to 0 of 0 entries</div>
                        <ul class="pagination pagination-sm mb-0 js-admin-pagination"></ul>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <?php require_once('./assets/inc/admin_bottom.php'); ?>
</body>

</html>