<?php
$pageTitle = "My Profile | MATrends";

$description = "Manage your MATrends profile, personal information, orders, addresses and account settings.";

$keywords = "MATrends profile, my account, user dashboard, account settings";

$author = "MATrends";
$robots = "noindex, nofollow";

$ogTitle = "My Profile | MATrends";
$ogDescription = "Manage your profile, orders and account settings at MATrends.";
$ogType = "website";
$ogUrl = "https://www.matrends.store/profile";

require_once("inc/top.php");
require_once('inc/top.php');

if (!isLoggedIn()) {
    header("Location: login");
    exit();
}

$userId = $_SESSION['user']['id'] ?? $_SESSION['admin']['id'];
$success = "";
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $sqlUpdate = "UPDATE users SET phone = '$phone', address = '$address' WHERE id = $userId";
    if (mysqli_query($conn, $sqlUpdate)) {
        $success = "Profile updated successfully!";
    } else {
        $error = "Error updating profile: " . mysqli_error($conn);
    }
}

// Fetch user data
$sqlUser = "SELECT *, DATE_FORMAT(created_at, '%M %Y') as joined_date FROM users WHERE id = $userId";
$resUser = mysqli_query($conn, $sqlUser);
$userData = mysqli_fetch_assoc($resUser);

// Fetch cart and order counts
$cartCount = 0;
$sqlCart = "SELECT COUNT(id) as count FROM cart WHERE user_id = $userId";
$resCart = mysqli_query($conn, $sqlCart);
if ($resCart) {
    $cartCount = mysqli_fetch_assoc($resCart)['count'];
}

$orderCount = 0;
$sqlOrdersCount = "SELECT COUNT(id) as count FROM orders WHERE user_id = $userId";
$resOrdersCount = mysqli_query($conn, $sqlOrdersCount);
if ($resOrdersCount) {
    $orderCount = mysqli_fetch_assoc($resOrdersCount)['count'];
}

$pageTitle = "My Account | MATrends";
?>

<?php require_once('inc/navbar.php'); ?>

<main class="ma-hero ma-section pt-5">
    <div class="container mt-5">
        <div class="row g-4">
            <!-- Sidebar / Stats -->
            <div class="col-lg-4">
                <div class="ma-card p-4 ma-shadow text-center mb-4">
                    <div class="position-relative d-inline-block mb-3">
                        <div class="ma-pill p-0 overflow-hidden border-2 border-gold" style="width: 100px; height: 100px;">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($userData['name']); ?>&background=d7b46a&color=0b0c0f&size=100" alt="Avatar">
                        </div>
                        <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-success border border-dark" style="width: 15px; height: 15px; padding: 0;">&nbsp;</span>
                    </div>
                    <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($userData['name']); ?></h4>
                    <p class="ma-muted small mb-3"><?php echo htmlspecialchars($userData['email']); ?></p>
                    <span class="badge badge-ma rounded-pill px-3 py-2">Member since <?php echo $userData['joined_date'] ?? 'Recently'; ?></span>

                    <hr class="border ma-border my-4">

                    <div class="row g-2">
                        <div class="col-6">
                            <div class="ma-card p-2 ma-bg-surface-2 border-0">
                                <div class="h5 mb-0 fw-bold color-gold"><?php echo $cartCount; ?></div>
                                <div class="small ma-muted">Cart Items</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="ma-card p-2 ma-bg-surface-2 border-0">
                                <div class="h5 mb-0 fw-bold color-gold"><?php echo $orderCount; ?></div>
                                <div class="small ma-muted">Orders</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ma-card p-2 ma-shadow">
                    <div class="nav flex-column nav-pills ma-profile-nav">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-profile">
                            <i class="fas fa-user-edit me-2"></i> Profile Information
                        </button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-orders">
                            <i class="fas fa-shopping-bag me-2"></i> My Orders
                        </button>

                        <a href="logout" class="nav-link text-danger mt-2 js-logout">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-lg-8">
                <div class="tab-content">
                    <!-- Profile Tab -->
                    <div class="tab-pane fade show active" id="tab-profile">
                        <div class="ma-card p-4 p-md-5 ma-shadow">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h2 class="h4 fw-bold mb-0">Profile Information</h2>
                                <i class="fas fa-id-card color-gold fs-4"></i>
                            </div>

                            <?php if ($success): ?>
                                <div class="alert alert-ma-success mb-4 d-flex align-items-center">
                                    <i class="fas fa-check-circle me-2"></i> <?php echo $success; ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label ma-muted small">Full Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-transparent border-end-0 ma-border"><i class="fas fa-user ma-muted"></i></span>
                                            <input type="text" class="form-control border-start-0" value="<?php echo htmlspecialchars($userData['name']); ?>" readonly />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label ma-muted small">Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-transparent border-end-0 ma-border"><i class="fas fa-envelope ma-muted"></i></span>
                                            <input type="email" class="form-control border-start-0" value="<?php echo htmlspecialchars($userData['email']); ?>" readonly />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label ma-muted small">Phone Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-transparent border-end-0 ma-border"><i class="fas fa-phone color-gold"></i></span>
                                            <input type="text" class="form-control border-start-0" name="phone" placeholder="+92 3XX XXXXXXX" value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>" required />
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label ma-muted small">Shipping Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-transparent border-end-0 ma-border"><i class="fas fa-map-marker-alt color-gold"></i></span>
                                            <textarea class="form-control border-start-0" name="address" rows="4" placeholder="Street address, city, postal code..." required><?php echo htmlspecialchars($userData['address'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <button type="submit" name="update_profile" class="btn btn-ma px-5">
                                            <i class="fas fa-save me-2"></i> Save Profile
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Orders Tab -->
                    <div class="tab-pane fade" id="tab-orders">
                        <?php if ($orderCount > 0): ?>
                            <div class="d-flex flex-column gap-3">
                                <?php
                                $sqlMyOrders = "SELECT * FROM orders WHERE user_id = $userId ORDER BY created_at DESC";
                                $resMyOrders = mysqli_query($conn, $sqlMyOrders);
                                while ($order = mysqli_fetch_assoc($resMyOrders)) {
                                    $statusBadge = '<span class="badge bg-warning text-dark">Verifying</span>';
                                    if ($order['status'] == 1) {
                                        $statusBadge = '<span class="badge bg-success">Delivered</span>';
                                    } elseif ($order['status'] == 2) {
                                        $statusBadge = '<span class="badge bg-danger">Cancelled</span>';
                                    } elseif ($order['status'] == 0 && isset($order['order_confirmation']) && $order['order_confirmation'] == 1) {
                                        $statusBadge = '<span class="badge bg-info">In Progress</span>';
                                    }
                                ?>
                                    <div class="ma-card p-3 p-md-4 ma-shadow d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <h5 class="fw-bold mb-0">Order #ORD-<?php echo $order['id']; ?></h5>
                                                <?php echo $statusBadge; ?>
                                            </div>
                                            <p class="ma-muted small mb-0">Placed on <?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                                        </div>
                                        <div class="d-flex flex-column align-items-md-end gap-2">
                                            <div class="fw-bold color-gold">Rs. <?php echo number_format($order['total'], 2); ?></div>
                                            <button class="btn btn-sm btn-ma-outline" data-bs-toggle="modal" data-bs-target="#orderDetailsModal_<?php echo $order['id']; ?>">Details</button>
                                        </div>
                                    </div>

                                    <!-- Order Details Modal -->
                                    <div class="modal fade" id="orderDetailsModal_<?php echo $order['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title fw-bold text-white">Order Details</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body ma-muted">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="ma-muted">Order ID: ORD-<?php echo $order['id']; ?></span>
                                                        <?php echo $statusBadge; ?>
                                                    </div>
                                                    
                                                    <div class="table-responsive">
                                                        <table class="table table-dark table-striped align-middle mb-0 border ma-border">
                                                            <thead>
                                                                <tr>
                                                                    <th class="border-bottom ma-border">Item</th>
                                                                    <th class="text-center border-bottom ma-border">Qty</th>
                                                                    <th class="text-end border-bottom ma-border">Subtotal</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $orderId = $order['id'];
                                                                $sqlDetails = "SELECT * FROM order_details WHERE order_id = $orderId";
                                                                $resDetails = mysqli_query($conn, $sqlDetails);
                                                                while ($item = mysqli_fetch_assoc($resDetails)):
                                                                ?>
                                                                <tr>
                                                                    <td class="border-bottom ma-border"><?php echo htmlspecialchars($item['product_name']); ?></td>
                                                                    <td class="text-center border-bottom ma-border"><?php echo $item['qty']; ?></td>
                                                                    <td class="text-end border-bottom ma-border">Rs. <?php echo number_format($item['sub_total'], 2); ?></td>
                                                                </tr>
                                                                <?php endwhile; ?>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td colspan="2" class="text-end ma-muted border-0 pt-3">Shipping:</td>
                                                                    <td class="text-end border-0 pt-3">Rs. <?php echo number_format($order['shipping'], 2); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2" class="text-end fw-bold color-gold border-0 pb-3">Total:</td>
                                                                    <td class="text-end fw-bold color-gold border-0 pb-3">Rs. <?php echo number_format($order['total'], 2); ?></td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0">
                                                    <button type="button" class="btn btn-ma-ghost" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php else: ?>
                            <div class="ma-card p-4 p-md-5 ma-shadow text-center">
                                <i class="fas fa-box-open fa-4x ma-muted mb-4"></i>
                                <h2 class="h4 fw-bold">No Orders Yet</h2>
                                <p class="ma-muted mb-4">You haven't placed any orders with us yet. Start exploring our latest trends!</p>
                                <a href="shop" class="btn btn-ma">Explore Shop</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Settings Tab (Placeholder) -->

                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once('inc/footer.php');
require_once('inc/bottom.php');
?>