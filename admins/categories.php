<?php
require_once('./assets/inc/admin_top.php');


// update status
if (isset($_POST["updateStatus"])) {
    $id = $_POST["id"];
    $status = $_POST["status"];
    $new_status = ($status == 1) ? 0 : 1;
    $sql = "UPDATE categories SET status = $new_status WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    // change status of sub category too
    $sql_subCategories = "UPDATE sub_categories SET status = $new_status WHERE category_id = $id";
    $result_subCategories = mysqli_query($conn, $sql_subCategories);
    echo $new_status;
    exit;
}

// delete category
if (isset($_GET['action']) && $_GET['action'] == 'delete') {

    $id = $_GET['id'];
    $resultCategory = mysqli_query($conn, "DELETE FROM categories WHERE id = $id");
    if ($resultCategory) {
        header("Location: categories");
    }
    exit;
}

// add category
if (isset($_POST['add_category'])) {
    $name = getSaveValue($conn,  $_POST['name']);
    $slug = getSaveValue($conn,  str: $_POST['slug']);
    $status = (int)$_POST['status'];
    $sql = "INSERT INTO categories (name, slug, status) VALUES ('$name', '$slug', '$status')";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo 1;
    } else {
        echo 0;
    }
    exit;
}

// edit category
if (isset($_POST['edit_category'])) {
    $id = $_POST['id'];
    $name = getSaveValue($conn,  $_POST['name']);
    $slug = getSaveValue($conn,  str: $_POST['slug']);
    $status = (int)$_POST['status'];
    $sql = "UPDATE categories set name='$name', slug='$slug', status='$status' where id='$id'";
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

    <div class="ma-admin-shell d-flex flex-column flex-md-row">
        <?php
        require_once('./assets/inc/admin_sidebar.php');
        ?>

        <main class="flex-grow-1">
            <!-- Mobile admin menu (small screens) -->
            <?php
            require_once('./assets/inc/admin_sidebar_responsive.php');
            //  Mobile admin menu (small screens) end 

            // admin header start
            require_once('./assets/inc/admin_header.php');
            // admin header end
            ?>

            <div class="d-flex justify-content-end">
                <button class="btn btn-ma mt-3 me-4" data-bs-toggle="modal" data-bs-target="#addCategory">Add category</button>
            </div>
            <div class="p-3 p-md-4">

                <!-- Admin table wrapper: rounded container, dark gradient -->
                <div class="ma-admin-table-wrap ma-card p-3 p-md-4">
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
                                    <th>Slug</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="js-admin-tbody">
                                <?php
                                $sqlCategories = "SELECT * FROM categories";
                                $resultCategories = mysqli_query($conn, $sqlCategories);
                                if (mysqli_num_rows($resultCategories) > 0) {
                                    $sr = 0;
                                    while ($row = mysqli_fetch_assoc($resultCategories)) {
                                        $sr++;
                                ?>
                                        <tr class="js-admin-row" data-sr="<?php echo $sr; ?>" data-id="<?php echo $row["id"]; ?>" data-name="<?php echo $row["name"]; ?>" data-slug="<?php echo $row["slug"]; ?>" data-status="<?php echo $row["status"]; ?>">
                                            <td><?php echo $sr; ?></td>
                                            <td>CTG-<?php echo $row["id"]; ?></td>
                                            <td class="text-capitalize"><?php echo $row["name"]; ?></td>
                                            <td><?php echo $row["slug"]; ?></td>
                                            <td class="btn-status">
                                                <?php $btnClass = ($row['status'] == 1) ? 'badge-trending' : 'badge-ma'; ?>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-ma-outline <?php echo $btnClass; ?> rounded-pill fw-bold px-2 py-0 small text-capitalize" id="status-update"
                                                    data-id="<?php echo $row["id"]; ?>"
                                                    data-status="<?php echo $row["status"]; ?>">
                                                    <?php echo ($row['status'] == 1) ? 'active' : 'hidden'; ?>
                                                </button>


                                            </td>
                                            <td class="text-end">
                                                <!-- edit button -->
                                                <button class="btn btn-sm btn-ma-outline edit-btn" data-bs-toggle="modal" data-bs-target="#editCategory" data-id="<?php echo $row["id"]; ?>" data-name="<?php echo $row["name"]; ?>" data-slug="<?php echo $row["slug"] ?>" data-status="<?php echo $row["status"]; ?>">Edit</button>
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
        </main>
    </div>

    <!-- Add Category -->
    <div class="modal fade" id="addCategory" tabindex="-1" aria-hidden="true">
        <form class="modal-dialog modal-dialog-centered" method="post" action="categories" id="add_category_form">
            <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Add category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label ma-muted">Name</label>
                        <input class="form-control" placeholder="Category name" name="name" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label ma-muted">Slug</label>
                        <input class="form-control" placeholder="/category-name" name="slug" value="/category-" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label ma-muted">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="" disabled selected>Select Status</option>
                            <option value="0">Hidden</option>
                            <option value="1">Active</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <!-- Hidden flag for PHP -->
                    <input type="hidden" name="add_category" value="1">
                    <button class="btn btn-ma" type="submit" id="add_category_btn">Save Category</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Edit Category -->
    <div class="modal fade" id="editCategory" tabindex="-1" aria-hidden="true">
        <form class="modal-dialog modal-dialog-centered" method="post" action="categories" id="edit_category_form">
            <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Edit category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <input type="hidden" class="form-control" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label ma-muted">Name</label>
                        <input class="form-control" placeholder="Category name" name="name" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label ma-muted">Slug</label>
                        <input class="form-control" placeholder="/category-name" name="slug" value="/category-" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label ma-muted">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="" disabled selected>Select Status</option>
                            <option value="0">Hidden</option>
                            <option value="1">Active</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <!-- Hidden flag for PHP -->
                    <input type="hidden" name="edit_category" value="1">
                    <button class="btn btn-ma" type="submit" id="edit_category_btn">Save Category</button>
                </div>
            </div>
        </form>
    </div>


    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Delete category?</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body ma-muted">Are you sure to delete <span class="badge badge-trending rounded-pill text-capitalize fs-6">category name</span></div>
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

    <script src="./assets/js/categories.js"></script>
</body>

</html>