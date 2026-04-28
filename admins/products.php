<?php
require_once('./assets/inc/admin_top.php');


// update status
if (isset($_POST['updateStatus'])) {
    $id     = (int)$_POST['id'];
    $status = $_POST['status'];

    // block toggle if parent category is hidden
    $productRow   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT category_id FROM products WHERE id = $id"));
    $categoryRow  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status FROM categories WHERE id = '{$productRow['category_id']}'"));
    if ($categoryRow['status'] == 0) {
        echo 'locked';
        exit;
    }

    $new_status = ($status == 1) ? 0 : 1;
    $sql = "UPDATE products SET status = $new_status WHERE id = $id";
    mysqli_query($conn, $sql);
    echo $new_status;
    exit;
}

// delete product
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    $resultProduct = mysqli_query($conn, "DELETE FROM products WHERE id = $id");
    if ($resultProduct) {
        header("Location: products");
    }
    exit;
}

// add product
if (isset($_POST['add_product'])) {
    $name        = getSaveValue($conn, $_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $status      = (int)$_POST['status'];
    $price       = getSaveValue($conn, $_POST['price']);
    $old_price   = getSaveValue($conn, $_POST['old_price']);
    $discount    = getSaveValue($conn, $_POST['discount']);
    $properties  = getSaveValue($conn, $_POST['properties']);
    $description = getSaveValue($conn, $_POST['description']);
    $sql = "INSERT INTO products (name, category_id, status, price, old_price, discount, properties, description)
            VALUES ('$name', '$category_id', '$status', '$price', '$old_price', '$discount', '$properties', '$description')";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo 1;
    } else {
        echo 0;
    }
    exit;
}

// edit product
if (isset($_POST['edit_product'])) {
    $id          = (int)$_POST['id'];
    $name        = getSaveValue($conn, $_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $status      = (int)$_POST['status'];
    $price       = getSaveValue($conn, $_POST['price']);
    $old_price   = getSaveValue($conn, $_POST['old_price']);
    $discount    = getSaveValue($conn, $_POST['discount']);
    $properties  = getSaveValue($conn, $_POST['properties']);
    $description = getSaveValue($conn, $_POST['description']);
    $sql = "UPDATE products SET name='$name', category_id='$category_id', status='$status', price='$price',
            old_price='$old_price', discount='$discount', properties='$properties', description='$description'
            WHERE id='$id'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo 1;
    } else {
        echo 0;
    }
    exit;
}


?>

<body>
    <div class="ma-admin-shell d-flex flex-column flex-md-row ma-admin-collapsed">
        <?php
        require_once('./assets/inc/admin_sidebar.php');
        ?>

        <main class="flex-grow-1" style="min-width: 0;">
            <!-- Mobile admin menu (small screens) -->
            <?php
            require_once('./assets/inc/admin_sidebar_responsive.php');
            //  Mobile admin menu (small screens) end 

            // admin header start
            require_once('./assets/inc/admin_header.php');
            // admin header end
            ?>
            <div class="d-flex justify-content-end">
                <button class="btn btn-ma mt-3 me-4" data-bs-toggle="modal" data-bs-target="#addProduct">Add Product</button>
            </div>

            <div class="p-3">

                <!-- Admin table wrapper: rounded container, dark gradient -->
                <div class="ma-admin-table-wrap ma-card p-3 p-md-4 w-100" style="max-width: 100%; overflow: hidden;">
                    <!-- Header: entries per page (left), search (right) -->
                    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-3 mb-3">
                        <div class="d-flex align-items-center gap-5">
                            <div class="d-flex align-items-center gap-2">
                                <label class="ma-muted small mb-0 text-nowrap">Show</label>
                                <select class="form-select form-select-sm js-admin-entries" style="width: auto; min-width: 70px;">
                                    <option value="5" selected>5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span class="ma-muted small text-nowrap">entries</span>
                            </div>
                            <div> <label class="ma-muted small mb-0  text-nowrap fw-bold">Sort</label>
                                <span class="ma-sort-controls" data-sort="name">
                                    <i class="fa-solid fa-arrow-up ma-sort-arrow small active" data-dir="asc"></i>
                                    <i class="fa-solid fa-arrow-down ma-sort-arrow small" data-dir="desc"></i>
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
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Properties</th>
                                    <th>Description</th>
                                    <th>New Price</th>
                                    <th>Old Price</th>
                                    <th>Discount</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="js-admin-tbody">
                                <?php
                                $sqlProducts = "SELECT * FROM products";
                                $resProducts = mysqli_query($conn, $sqlProducts);
                                if (mysqli_num_rows($resProducts) > 0) {
                                    $sr = 0;
                                    while (($row = mysqli_fetch_assoc($resProducts)) > 0) {
                                        $sr++;

                                ?>
                                        <tr class="js-admin-row" data-sr="<?php echo $sr; ?>" data-id="<?php echo $row["id"]; ?>" data-name="<?php echo $row["name"]; ?>">
                                            <td><?php echo $sr; ?></td>
                                            <td>PRD-<?php echo $row["id"]; ?></td>
                                            <td class="text-capitalize"><?php echo $row["name"]; ?></td>
                                            <td class="text-capitalize"><?php
                                                                        $categoryID   = $row['category_id'];
                                                                        $category     = mysqli_query($conn, "select name, status from categories where id='$categoryID'");
                                                                        $row_category = mysqli_fetch_assoc($category);
                                                                        $catActive    = ($row_category['status'] == 1);
                                                                        echo $row_category['name'];
                                                                        ?></td>


                                            <td class="text-capitalize">
                                                <span data-bs-toggle="tooltip" data-bs-placement="right" title="<?php echo htmlspecialchars($row["properties"]); ?>">
                                                    <?php
                                                    $words = explode(" ", $row["properties"]);
                                                    echo implode(" ", array_slice($words, 0, 2)) . (count($words) > 3 ? '...' : '');
                                                    ?>
                                                </span>
                                            </td>
                                            <td class="text-capitalize">
                                                <span data-bs-toggle="tooltip" data-bs-placement="right" title="<?php echo htmlspecialchars($row["description"]); ?>">
                                                    <?php
                                                    $words = explode(" ", $row["description"]);
                                                    echo implode(" ", array_slice($words, 0, 2)) . (count($words) > 3 ? '...' : '');
                                                    ?>
                                                </span>
                                            </td>
                                            <td>Rs. <?php echo $row["price"]; ?></td>
                                            <td>Rs. <?php echo $row["old_price"]; ?></td>
                                            <td><?php echo $row["discount"]; ?>%</td>


                                            <td class="btn-status">
                                                <?php
                                                    $btnClass  = ($catActive && $row['status'] == 1) ? 'badge-trending' : 'badge-ma';
                                                    $btnLabel  = ($catActive && $row['status'] == 1) ? 'active' : 'hidden';
                                                ?>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-ma-outline <?php echo $btnClass; ?> rounded-pill fw-bold px-2 py-0 small text-capitalize"
                                                    <?php if ($catActive): ?>
                                                        id="status-update"
                                                        data-id="<?php echo $row["id"]; ?>"
                                                        data-status="<?php echo $row["status"]; ?>"
                                                    <?php else: ?>
                                                        disabled
                                                    <?php endif; ?>>
                                                    <?php echo $btnLabel; ?>
                                                </button>


                                            </td>
                                            <td class="text-end">
                                                <!-- edit button -->
                                                <button class="btn btn-sm btn-ma-outline edit-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editProduct"
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                                    data-categoryid="<?php echo $row['category_id']; ?>"
                                                    data-status="<?php echo $row['status']; ?>"
                                                    data-price="<?php echo $row['price']; ?>"
                                                    data-oldprice="<?php echo $row['old_price']; ?>"
                                                    data-discount="<?php echo $row['discount']; ?>"
                                                    data-properties="<?php echo htmlspecialchars(html_entity_decode($row['properties'])); ?>"
                                                    data-description="<?php echo htmlspecialchars($row['description']); ?>">Edit</button>
                                                <!-- delete button -->
                                                <button
                                                    class="btn btn-sm btn-ma-ghost text-danger delete-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-name="<?php echo $row['name']; ?>">
                                                    Delete
                                                </button>


                                            </td>
                                        </tr>


                                <?php


                                    }
                                }
                                ?>


                            </tbody>
                        </table>
                    </div>
                    <!-- Footer: showing X to Y of Z entries (left), pagination (right) -->
                    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 mt-3 pt-3 border-top ma-border">
                        <div class="ma-muted small js-admin-footer-info">Showing 1 to 3 of 3 entries</div>
                        <ul class="pagination pagination-sm mb-0 js-admin-pagination"></ul>
                    </div>
                </div>
            </div>

            <!-- Add Modal -->
            <div class="modal fade" id="addProduct" tabindex="-1" aria-hidden="true">
                <form class="modal-dialog modal-dialog-centered modal-lg" method="post" action="products" id="add_product_form">
                    <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                        <div class="modal-header border-0">
                            <h5 class="modal-title text-white">Add product</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label ma-muted">Name</label>
                                    <input class="form-control" type="text" placeholder="Product name" name="name" required />
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label ma-muted">Category</label>
                                    <select class="form-select" name="category_id" required>
                                        <option value="" disabled selected>Select Category</option>
                                        <?php
                                        $categories = mysqli_query($conn, "SELECT * FROM categories WHERE status = 1");
                                        if (mysqli_num_rows($categories) > 0) {
                                            while ($category = mysqli_fetch_assoc($categories)) {


                                        ?>
                                                <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                        <?php
                                            }
                                        } else {
                                            echo '<option value="" disabled>No active category found</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3">

                                    <label class="form-label ma-muted">Status</label>
                                    <select class="form-select" name="status" required>
                                        <option value="" disabled selected>Select Status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Hidden</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label ma-muted">Price</label>
                                    <input class="form-control" type="number" placeholder="Rs 0.000" name="price" required />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label ma-muted">Old Price</label>
                                    <input class="form-control" type="number" placeholder="Rs 0.000" name="old_price" required />

                                </div>
                                <div class="col-md-4">
                                    <label class="form-label ma-muted">Discount</label>
                                    <input class="form-control" type="number" placeholder="Discount %" name="discount" required />

                                </div>

                                <div class="col-12">
                                    <label class="form-label ma-muted">Properties</label>
                                    <input class="form-control" placeholder="Stainless Steel • Water Resistant • Sapphire Glass" type="text" name="properties" required />
                                </div>
                                <div class="col-12">
                                    <label class="form-label ma-muted">Description</label>
                                    <textarea class="form-control" rows="3" placeholder="Short description" name="description" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <!-- Hidden flag for PHP -->
                            <input type="hidden" name="add_product" value="1">
                            <button class="btn btn-ma" type="submit" id="add_product_btn">Save Product</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="editProduct" tabindex="-1" aria-hidden="true">
                <form class="modal-dialog modal-dialog-centered modal-lg" method="post" action="products" id="edit_product_form">
                    <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                        <div class="modal-header border-0">
                            <h5 class="modal-title text-white">Edit product</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <!-- Hidden product id -->
                        <input type="hidden" name="id">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label ma-muted">Name</label>
                                    <input class="form-control" type="text" placeholder="Product name" name="name" required />
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label ma-muted">Category</label>
                                    <select class="form-select" name="category_id" required>
                                        <option value="" disabled selected>Select Category</option>
                                        <?php
                                        $categories = mysqli_query($conn, "SELECT * FROM categories WHERE status = 1");
                                        if (mysqli_num_rows($categories) > 0) {
                                            while ($category = mysqli_fetch_assoc($categories)) {


                                        ?>
                                                <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                        <?php
                                            }
                                        } else {
                                            echo '<option value="" disabled>No active category found</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3">

                                    <label class="form-label ma-muted">Status</label>
                                    <select class="form-select" name="status" required>
                                        <option value="" disabled selected>Select Status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Hidden</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label ma-muted">Price</label>
                                    <input class="form-control" type="number" placeholder="Rs 0.000" name="price" required />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label ma-muted">Old Price</label>
                                    <input class="form-control" type="number" placeholder="Rs 0.000" name="old_price" required />

                                </div>
                                <div class="col-md-4">
                                    <label class="form-label ma-muted">Discount</label>
                                    <input class="form-control" type="number" placeholder="Discount %" name="discount" required />

                                </div>

                                <div class="col-12">
                                    <label class="form-label ma-muted">Properties</label>
                                    <input class="form-control" placeholder="Stainless Steel • Water Resistant • Sapphire Glass" type="text" name="properties" required />
                                </div>
                                <div class="col-12">
                                    <label class="form-label ma-muted">Description</label>
                                    <textarea class="form-control" rows="3" placeholder="Short description" name="description" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <!-- Hidden flag for PHP -->
                            <input type="hidden" name="edit_product" value="1">
                            <button class="btn btn-ma" type="submit" id="edit_product_btn">Save Product</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Delete Modal -->
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                        <div class="modal-header border-0">
                            <h5 class="modal-title text-white">Delete Product?</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body ma-muted">Are you sure to delete <span class="badge badge-trending rounded-pill text-capitalize fs-6">Product name</span></div>
                        <div class="modal-footer border-0">
                            <button class="btn btn-ma-ghost" data-bs-dismiss="modal" type="button">Cancel</button>
                            <a class="btn btn-ma" id="confirmDelete">Delete</a>
                        </div>
                    </div>
                </div>
            </div>





            <?php
            require_once('./assets/inc/admin_bottom.php');
            ?>

    <script src="./assets/js/products.js"></script>
</body>

</html>