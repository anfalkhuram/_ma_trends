<?php
// ── AJAX handlers (before any output) ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    ini_set('display_errors', 0);
    error_reporting(0);
    ob_start();
    require_once('./assets/inc/config.php');
    ob_clean();

    header('Content-Type: application/json');

    $action = trim($_POST['ajax_action']);
    $id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id > 0) {
        if ($action === 'delete') {
            $del = mysqli_query($conn, "DELETE FROM product_feedback WHERE id = $id");
            if ($del) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'DB delete error: ' . mysqli_error($conn)]);
            }
            exit;
        }

        if ($action === 'toggle_status') {
            $newStatus = isset($_POST['status']) ? (int)$_POST['status'] : 0;
            $upd = mysqli_query($conn, "UPDATE product_feedback SET status = $newStatus WHERE id = $id");

            if (!$upd) {
                echo json_encode(['success' => false, 'message' => 'DB update error: ' . mysqli_error($conn)]);
                exit;
            }

            // Recalculate product average from approved reviews
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            if ($productId > 0) {
                $avgRes = mysqli_query($conn, "SELECT ROUND(AVG(rating), 1) AS avg_rating FROM product_feedback WHERE product_id = $productId AND status = 1");
                if ($avgRes) {
                    $avgRow = mysqli_fetch_assoc($avgRes);
                    $newAvg = $avgRow['avg_rating'] !== null ? (float)$avgRow['avg_rating'] : 0;
                    mysqli_query($conn, "UPDATE products SET ratings = $newAvg WHERE id = $productId");
                }
            }

            echo json_encode(['success' => true]);
            exit;
        }
    }

    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// ── Normal page load ────────────────────────────────────────────────────────
require_once('./assets/inc/admin_top.php');
?>

<body>
    <div class="ma-admin-shell d-flex flex-column flex-md-row ma-admin-collapsed">
        <?php require_once('./assets/inc/admin_sidebar.php'); ?>

        <main class="flex-grow-1" style="min-width: 0;">
            <!-- Mobile admin menu -->
            <?php require_once('./assets/inc/admin_sidebar_responsive.php'); ?>

            <!-- Admin header -->
            <?php require_once('./assets/inc/admin_header.php'); ?>

            <div class="p-3 mt-3">
                <div class="ma-admin-table-wrap ma-card p-3 p-md-4 w-100" style="max-width: 100%; overflow: hidden;">

                    <!-- Table header controls -->
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

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-admin align-middle mb-0" id="myTable">
                            <thead>
                                <tr>
                                    <th>#Sr</th>
                                    <th>ID</th>
                                    <th>User Name</th>
                                    <th>Product</th>
                                    <th>Rating</th>
                                    <th>Feedback</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="js-admin-tbody">
                                <?php
                                $sql = "SELECT pf.*, p.name AS product_name
                                        FROM product_feedback pf
                                        LEFT JOIN products p ON p.id = pf.product_id
                                        ORDER BY pf.created_at DESC";
                                $res = mysqli_query($conn, $sql);

                                if ($res && mysqli_num_rows($res) > 0):
                                    $sr = 0;
                                    while ($row = mysqli_fetch_assoc($res)):
                                        $sr++;

                                        // Star string
                                        $stars = '';
                                        for ($s = 1; $s <= 5; $s++) {
                                            $stars .= $s <= $row['rating'] ? '★' : '☆';
                                        }

                                        // Truncate long feedback for display
                                        $feedbackDisplay = mb_strlen($row['feedback']) > 80
                                            ? mb_substr($row['feedback'], 0, 80) . '…'
                                            : $row['feedback'];

                                        $searchStr = $row['name'] . ' ' . $row['product_name'];
                                ?>
                                        <tr class="js-admin-row"
                                            data-sr="<?php echo $sr; ?>"
                                            data-id="<?php echo $row['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($searchStr); ?>">
                                            <td><?php echo $sr; ?></td>
                                            <td>FB-<?php echo $row['id']; ?></td>
                                            <td class="text-capitalize"><?php echo htmlspecialchars($row['name']); ?></td>
                                            <td class="text-capitalize"><?php echo htmlspecialchars($row['product_name'] ?? 'N/A'); ?></td>
                                            <td style="letter-spacing: 2px; color: var(--ma-accent);"><?php echo $stars; ?></td>
                                            <td class="ma-muted small" style="max-width: 260px; white-space: normal;">
                                                <span
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="right"
                                                    title="<?php echo htmlspecialchars($row['feedback'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    <?php
                                                    $shortText = mb_substr($row['feedback'], 0, 7);

                                                    echo htmlspecialchars(
                                                        $shortText . (mb_strlen($row['feedback']) > 7 ? '...' : ''),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($row['status'] == 1): ?>
                                                    <button class="badge rounded-pill px-3 py-1 border-0 badge-trending js-toggle-status"
                                                        data-id="<?php echo $row['id']; ?>"
                                                        data-product-id="<?php echo $row['product_id']; ?>"
                                                        data-current-status="1"
                                                        title="Click to hide this review">
                                                        Visible
                                                    </button>
                                                <?php else: ?>
                                                    <button class="badge rounded-pill px-3 py-1 border-0 badge-ma js-toggle-status"
                                                        data-id="<?php echo $row['id']; ?>"
                                                        data-product-id="<?php echo $row['product_id']; ?>"
                                                        data-current-status="0"
                                                        title="Click to approve &amp; show this review">
                                                        Hidden
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-ma-ghost text-danger js-delete-feedback py-1 px-3 small"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteFeedbackModal"
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($row['name']); ?>">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                <?php
                                    endwhile;
                                else:
                                    echo '<tr><td colspan="8" class="text-center py-4 ma-muted">No feedback found.</td></tr>';
                                endif;
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer: info + pagination -->
                    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 mt-3 pt-3 border-top ma-border">
                        <div class="ma-muted small js-admin-footer-info">Showing entries</div>
                        <ul class="pagination pagination-sm mb-0 js-admin-pagination"></ul>
                    </div>
                </div>
            </div>


            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteFeedbackModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                        <div class="modal-header border-0">
                            <h5 class="modal-title text-white">Delete Feedback?</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body ma-muted">
                            Are you sure you want to permanently delete the feedback from
                            <span class="badge badge-trending rounded-pill fs-6" id="deleteFeedbackName"></span>?
                            This action cannot be undone.
                        </div>
                        <div class="modal-footer border-0">
                            <button class="btn btn-ma-ghost" data-bs-dismiss="modal" type="button">Cancel</button>
                            <button class="btn btn-ma" id="confirmDeleteFeedbackBtn" data-id="">Yes, Delete</button>
                        </div>
                    </div>
                </div>
            </div>


            <?php require_once('./assets/inc/admin_bottom.php'); ?>

            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    // ── 1. Populate Delete Modal ─────────────────────────────
                    document.addEventListener('click', function(e) {
                        const btn = e.target.closest('.js-delete-feedback');
                        if (!btn) return;
                        document.getElementById('deleteFeedbackName').textContent = btn.dataset.name;
                        document.getElementById('confirmDeleteFeedbackBtn').dataset.id = btn.dataset.id;
                    });

                    // ── 2. Confirm Delete ────────────────────────────────────
                    document.getElementById('confirmDeleteFeedbackBtn').addEventListener('click', function() {
                        const id = this.dataset.id;
                        const btn = this;
                        btn.innerText = 'Deleting…';
                        btn.disabled = true;

                        const fd = new FormData();
                        fd.append('action', 'delete');
                        fd.append('id', id);

                        fetch('feedback-ajax.php', {
                                method: 'POST',
                                body: fd
                            })
                            .then(r => r.json())
                            .then(data => {
                                btn.innerText = 'Yes, Delete';
                                btn.disabled = false;
                                if (data.success) {
                                    bootstrap.Modal.getInstance(document.getElementById('deleteFeedbackModal')).hide();
                                    const row = document.querySelector(`.js-admin-row[data-id="${id}"]`);
                                    if (row) row.remove();
                                } else {
                                    alert('Error deleting feedback.');
                                }
                            })
                            .catch(() => {
                                btn.innerText = 'Yes, Delete';
                                btn.disabled = false;
                                alert('A network error occurred.');
                            });
                    });

                    // ── 3. Toggle Status (Hidden ↔ Visible) ──────────────────
                    document.addEventListener('click', function(e) {
                        const toggleBtn = e.target.closest('.js-toggle-status');
                        if (!toggleBtn) return;

                        const id = toggleBtn.dataset.id;
                        const currentStatus = parseInt(toggleBtn.dataset.currentStatus, 10);
                        const newStatus = currentStatus === 1 ? 0 : 1;

                        toggleBtn.disabled = true;

                        const fd = new FormData();
                        fd.append('action', 'set_status');
                        fd.append('id', id);
                        fd.append('new_status', newStatus);

                        fetch('feedback-ajax.php', {
                                method: 'POST',
                                body: fd
                            })
                            .then(r => r.json())
                            .then(data => {
                                toggleBtn.disabled = false;
                                if (data.success) {
                                    if (newStatus === 1) {
                                        toggleBtn.textContent = 'Visible';
                                        toggleBtn.className = 'badge rounded-pill px-3 py-1 border-0 badge-trending js-toggle-status';
                                        toggleBtn.title = 'Click to hide this review';
                                    } else {
                                        toggleBtn.textContent = 'Hidden';
                                        toggleBtn.className = 'badge rounded-pill px-3 py-1 border-0 badge-ma js-toggle-status';
                                        toggleBtn.title = 'Click to approve & show this review';
                                    }
                                    toggleBtn.dataset.currentStatus = newStatus;
                                } else {
                                    alert('Error updating status.');
                                }
                            })
                            .catch(() => {
                                toggleBtn.disabled = false;
                                alert('A network error occurred.');
                            });
                    });

                });
            </script>

        </main>
    </div>

</body>

</html>