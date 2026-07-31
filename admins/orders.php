<?php
// Backend AJAX logic - MUST be before any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    require_once('./assets/inc/config.php');
    
    header('Content-Type: application/json');
    $action = $_POST['ajax_action'];
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    if ($id > 0) {
        $resOrder = mysqli_query($conn, "SELECT * FROM orders WHERE id = $id LIMIT 1");
        if ($resOrder && mysqli_num_rows($resOrder) > 0) {
            $order = mysqli_fetch_assoc($resOrder);
            
            // Note: from admins/orders.php, we have to go up a dir to include email_functions.php
            require_once(__DIR__ . '/../inc/email_functions.php');
            $host = "http" . (isset($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['HTTP_HOST'];
            $data = [
                'name' => $order['name'],
                'orderId' => $order['id'],
                'total' => $order['total'],
                'shopUrl' => $host . '/index.php'
            ];
            
            if ($action == 'cancel') {
                mysqli_query($conn, "UPDATE orders SET status = 2 WHERE id = $id");
                sendTransactionalEmail($order['email'], "Your MATrends Order Has Been Canceled", 'order_canceled', $data, 'user', $id);
                echo json_encode(['success' => true]);
                exit;
            } elseif ($action == 'deliver') {
                mysqli_query($conn, "UPDATE orders SET status = 1 WHERE id = $id");
                sendTransactionalEmail($order['email'], "Your MATrends Order Has Been Delivered", 'order_delivered', $data, 'user', $id);
                echo json_encode(['success' => true]);
                exit;
            } elseif ($action == 'confirm') {
                mysqli_query($conn, "UPDATE orders SET order_confirmation = 1 WHERE id = $id");
                sendTransactionalEmail($order['email'], "Your MATrends Order Has Been Confirmed", 'order_confirmed', $data, 'user', $id);
                echo json_encode(['success' => true]);
                exit;
            }
        }
    }
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Normal page load
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
                                <span class="ma-sort-controls" data-sort="created_at">
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
                                    <th>Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>City / Country</th>
                                    <th>Special Instructions</th>
                                    <th>Total Amount</th>
                                    <th>Payment</th>
                                    <th>Receipt</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="js-admin-tbody">
                                <?php
                                $sqlOrders = "SELECT * FROM orders ORDER BY created_at DESC";
                                $resOrders = mysqli_query($conn, $sqlOrders);
                                if ($resOrders && mysqli_num_rows($resOrders) > 0) {
                                    $sr = 0;
                                    while ($row = mysqli_fetch_assoc($resOrders)) {
                                        $sr++;
                                        $statusClass = 'badge-ma';
                                        $statusText = 'Pending';
                                        
                                        if ($row['status'] == 1) {
                                            $statusClass = 'badge-trending';
                                            $statusText = 'Delivered';
                                        } else if ($row['status'] == 2) {
                                            $statusClass = 'bg-danger text-white';
                                            $statusText = 'Cancelled';
                                        } else if ($row['status'] == 0 && isset($row['order_confirmation']) && $row['order_confirmation'] == 1) {
                                            $statusClass = 'bg-primary text-white';
                                            $statusText = 'Confirmed';
                                        }

                                        // Fetch order items
                                        $orderDetailsSql = "SELECT * FROM order_details WHERE order_id = " . $row['id'];
                                        $resDetails = mysqli_query($conn, $orderDetailsSql);
                                        $items = [];
                                        if ($resDetails) {
                                            while ($d = mysqli_fetch_assoc($resDetails)) {
                                                $items[] = $d;
                                            }
                                        }
                                        $itemsJson = htmlspecialchars(json_encode($items), ENT_QUOTES, 'UTF-8');
                                        
                                        $fullAddress = trim($row['address'] . ', ' . $row['city'] . ', ' . $row['region'] . ' ' . $row['postalcode']);
                                        $searchString = $row['name'] . ' ' . $row['email'] . ' ' . $row['phone'] . ' ORD-' . $row['id'];
                                ?>
                                    <tr class="js-admin-row" data-sr="<?php echo $sr; ?>" data-id="<?php echo $row['id']; ?>" data-name="<?php echo htmlspecialchars($searchString); ?>">
                                        <td><?php echo $sr; ?></td>
                                        <td>ORD-<?php echo $row['id']; ?></td>
                                        <td class="text-capitalize"><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td class="text-capitalize"><?php echo htmlspecialchars($row['city'] . ', ' . $row['region']); ?></td>
                                        <td style="max-width: 200px; white-space: normal;"><?php echo !empty($row['special_instructions']) ? htmlspecialchars($row['special_instructions']) : '<span class="ma-muted small">N/A</span>'; ?></td>
                                        <td>Rs. <?php echo number_format($row['total'], 2); ?></td>
                                        <td class="text-uppercase"><?php echo htmlspecialchars($row['payment_method']); ?></td>
                                        <td class="text-center">
                                            <div class="d-flex flex-column align-items-center gap-2">
                                                <?php if (!empty($row['receipt_image']) && $row['receipt_image'] !== 'null'): ?>
                                                    <img src="../assets/img/receipts/<?php echo $row['receipt_image']; ?>" alt="Receipt"
                                                         class="img-thumbnail js-receipt-thumb"
                                                         data-receipt="../assets/img/receipts/<?php echo $row['receipt_image']; ?>"
                                                         data-order-id="<?php echo $row['id']; ?>"
                                                         style="max-height: 40px; width: auto; object-fit: contain; cursor: zoom-in;"
                                                         title="Click to view receipt">
                                                <?php else: ?>
                                                    <span class="ma-muted small">N/A</span>
                                                <?php endif; ?>
                                                
                                                <?php if ($row['status'] == 0 && (!isset($row['order_confirmation']) || $row['order_confirmation'] == 0)): ?>
                                                    <button class="btn btn-sm btn-ma-outline py-0 px-2 small js-confirm-order" data-id="<?php echo $row['id']; ?>" data-bs-toggle="modal" data-bs-target="#confirmOrderModal" style="font-size: 0.7rem;">Confirm</button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($row['status'] == 0 && isset($row['order_confirmation']) && $row['order_confirmation'] == 1): ?>
                                                <button class="badge rounded-pill px-3 py-1 border-0 <?php echo $statusClass; ?> js-deliver-order"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deliverOrderModal"
                                                    data-id="<?php echo $row['id']; ?>"
                                                    title="Click to mark as Delivered"
                                                >
                                                    <?php echo $statusText; ?>
                                                </button>
                                            <?php else: ?>
                                                <span class="badge rounded-pill px-3 py-1 <?php echo $statusClass; ?>">
                                                    <?php echo $statusText; ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-nowrap"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                        <td class="text-end">
                                            <div class="d-flex flex-column gap-2 align-items-end">
                                                <a class="btn btn-sm btn-ma-outline js-order-details py-1 px-3 small" href="invoice?id=<?php echo $row['id']; ?>">Details</a>

                                                <?php if ($row['status'] == 0): ?>
                                                    <button class="btn btn-sm btn-ma-ghost text-danger js-cancel-order py-1 px-3 small"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#cancelOrderModal"
                                                        data-id="<?php echo $row['id']; ?>"
                                                    >Cancel</button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-ma-ghost text-muted py-1 px-3 small" disabled>Cancel</button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="11" class="text-center py-4 ma-muted">No orders found.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 mt-3 pt-3 border-top ma-border">
                        <div class="ma-muted small js-admin-footer-info">Showing entries</div>
                        <ul class="pagination pagination-sm mb-0 js-admin-pagination"></ul>
                    </div>
                </div>
            </div>


            <!-- Cancel Order Modal -->
            <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                        <div class="modal-header border-0">
                            <h5 class="modal-title text-white">Cancel Order?</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body ma-muted">
                            Are you sure you want to cancel Order <span class="badge badge-trending rounded-pill fs-6" id="cancelOrderIdText"></span>? 
                            This action cannot be undone.
                        </div>
                        <div class="modal-footer border-0">
                            <button class="btn btn-ma-ghost" data-bs-dismiss="modal" type="button">No, Keep It</button>
                            <button class="btn btn-ma" id="confirmCancelOrderBtn" data-id="">Yes, Cancel Order</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deliver Order Modal -->
            <div class="modal fade" id="deliverOrderModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                        <div class="modal-header border-0">
                            <h5 class="modal-title text-white">Mark as Delivered?</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body ma-muted">
                            Are you sure you want to mark Order <span class="badge badge-trending rounded-pill fs-6" id="deliverOrderIdText"></span> as Delivered?
                        </div>
                        <div class="modal-footer border-0">
                            <button class="btn btn-ma-ghost" data-bs-dismiss="modal" type="button">Cancel</button>
                            <button class="btn btn-ma" id="confirmDeliverOrderBtn" data-id="">Yes, Mark Delivered</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirm Order Modal -->
            <div class="modal fade" id="confirmOrderModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                        <div class="modal-header border-0">
                            <h5 class="modal-title text-white">Confirm Order?</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body ma-muted">
                            Are you sure you want to confirm Order <span class="badge badge-trending rounded-pill fs-6" id="confirmOrderIdText"></span>?
                        </div>
                        <div class="modal-footer border-0">
                            <button class="btn btn-ma-ghost" data-bs-dismiss="modal" type="button">Cancel</button>
                            <button class="btn btn-ma" id="confirmOrderBtnLink" data-id="">Yes, Confirm</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Receipt Preview Modal -->
            <div class="modal fade" id="receiptPreviewModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                        <div class="modal-header border-0 pb-2">
                            <h5 class="modal-title text-white d-flex align-items-center gap-2">
                                <i class="fa-solid fa-receipt" style="color: var(--ma-accent);"></i>
                                Receipt — <span id="receiptModalOrderId" class="badge badge-trending rounded-pill ms-1"></span>
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center p-3" style="background: rgba(0,0,0,.25); border-radius: 0 0 var(--ma-radius) var(--ma-radius);">
                            <img id="receiptModalImg" src="" alt="Receipt"
                                 style="max-width: 100%; max-height: 80vh; object-fit: contain; border-radius: 6px; box-shadow: 0 4px 24px rgba(0,0,0,.5);">
                        </div>
                        <div class="modal-footer border-0 pt-2">
                            <a id="receiptModalDownload" href="" target="_blank" class="btn btn-ma-outline btn-sm">
                                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Full Size
                            </a>
                            <button class="btn btn-ma-ghost" data-bs-dismiss="modal" type="button">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <?php require_once('./assets/inc/admin_bottom.php'); ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    // Event Delegation for populating modals
                    document.addEventListener('click', function(e) {
                        const deliverBtn = e.target.closest('.js-deliver-order');
                        if (deliverBtn) {
                            const id = deliverBtn.dataset.id;
                            document.getElementById('deliverOrderIdText').textContent = 'ORD-' + id;
                            document.getElementById('confirmDeliverOrderBtn').dataset.id = id;
                        }
                        
                        const confirmBtn = e.target.closest('.js-confirm-order');
                        if (confirmBtn) {
                            const id = confirmBtn.dataset.id;
                            document.getElementById('confirmOrderIdText').textContent = 'ORD-' + id;
                            document.getElementById('confirmOrderBtnLink').dataset.id = id;
                        }
                        
                        const cancelBtn = e.target.closest('.js-cancel-order');
                        if (cancelBtn) {
                            const id = cancelBtn.dataset.id;
                            document.getElementById('cancelOrderIdText').textContent = 'ORD-' + id;
                            document.getElementById('confirmCancelOrderBtn').dataset.id = id;
                        }

                        // Receipt lightbox
                        const receiptThumb = e.target.closest('.js-receipt-thumb');
                        if (receiptThumb) {
                            const src = receiptThumb.dataset.receipt;
                            const orderId = receiptThumb.dataset.orderId;
                            document.getElementById('receiptModalImg').src = src;
                            document.getElementById('receiptModalOrderId').textContent = 'ORD-' + orderId;
                            document.getElementById('receiptModalDownload').href = src;
                            const modal = new bootstrap.Modal(document.getElementById('receiptPreviewModal'));
                            modal.show();
                        }
                    });

                    // Generic AJAX Handler
                    function handleAjaxAction(btnId, action, modalId) {
                        document.getElementById(btnId).addEventListener('click', function() {
                            const id = this.dataset.id;
                            const originalText = this.innerText;
                            this.innerText = 'Processing...';
                            this.disabled = true;

                            const formData = new FormData();
                            formData.append('ajax_action', action);
                            formData.append('id', id);

                            fetch('orders.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.innerText = originalText;
                                this.disabled = false;
                                
                                if(data.success) {
                                    // Close modal
                                    const modalEl = document.getElementById(modalId);
                                    const modal = bootstrap.Modal.getInstance(modalEl);
                                    if(modal) modal.hide();

                                    // Update DOM
                                    const row = document.querySelector(`.js-admin-row[data-id="${id}"]`);
                                    if(row) {
                                        const receiptTd = row.children[8];
                                        const statusTd = row.children[9];
                                        const actionTd = row.children[11];

                                        if (action === 'confirm') {
                                            statusTd.innerHTML = `<button class="badge rounded-pill px-3 py-1 border-0 bg-primary text-white js-deliver-order" data-bs-toggle="modal" data-bs-target="#deliverOrderModal" data-id="${id}" title="Click to mark as Delivered">Confirmed</button>`;
                                            const cBtn = receiptTd.querySelector('.js-confirm-order');
                                            if(cBtn) cBtn.remove();
                                            // Cancel button remains active
                                        } 
                                        else if (action === 'deliver') {
                                            statusTd.innerHTML = `<span class="badge rounded-pill px-3 py-1 badge-trending">Delivered</span>`;
                                            const cBtn = receiptTd.querySelector('.js-confirm-order');
                                            if(cBtn) cBtn.remove();
                                            const cancelBtn = actionTd.querySelector('.js-cancel-order');
                                            if (cancelBtn) cancelBtn.outerHTML = `<button class="btn btn-sm btn-ma-ghost text-muted py-1 px-3 small" disabled>Cancel</button>`;
                                        }
                                        else if (action === 'cancel') {
                                            statusTd.innerHTML = `<span class="badge rounded-pill px-3 py-1 bg-danger text-white">Cancelled</span>`;
                                            const cBtn = receiptTd.querySelector('.js-confirm-order');
                                            if(cBtn) cBtn.remove();
                                            const cancelBtn = actionTd.querySelector('.js-cancel-order');
                                            if (cancelBtn) cancelBtn.outerHTML = `<button class="btn btn-sm btn-ma-ghost text-muted py-1 px-3 small" disabled>Cancel</button>`;
                                        }
                                    }
                                } else {
                                    alert('Error processing request.');
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                alert('A network error occurred.');
                                this.innerText = originalText;
                                this.disabled = false;
                            });
                        });
                    }

                    handleAjaxAction('confirmCancelOrderBtn', 'cancel', 'cancelOrderModal');
                    handleAjaxAction('confirmDeliverOrderBtn', 'deliver', 'deliverOrderModal');
                    handleAjaxAction('confirmOrderBtnLink', 'confirm', 'confirmOrderModal');
                });
            </script>
        </main>
    </div>
</body>
</html>