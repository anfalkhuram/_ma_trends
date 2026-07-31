<?php
require_once('./assets/inc/admin_top.php');


// add product details
if (isset($_POST['add_product_details'])) {

    $product_id = $_POST['product_id'];
    $option     = $_POST['option'];
    $value      = $_POST['value'];
    $gender     = $_POST['gender'];
    $label      = $_POST['label'];
    $stock      = $_POST['stock'];
    $ratings    = $_POST['ratings'];

    // FILE UPLOAD (WebP Conversion)
    $originalName = $_FILES["image"]["name"];
    $tmpName      = $_FILES['image']['tmp_name'];
    $imageName    = time() . "_" . pathinfo($originalName, PATHINFO_FILENAME) . ".webp";
    $destination  = "./assets/images/products/" . $imageName;

    $info = getimagesize($tmpName);
    $image = null;
    if ($info !== false) {
        $mime = $info['mime'];
        if ($mime == 'image/jpeg') {
            $image = imagecreatefromjpeg($tmpName);
        } elseif ($mime == 'image/png') {
            $image = imagecreatefrompng($tmpName);
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        } elseif ($mime == 'image/webp') {
            move_uploaded_file($tmpName, $destination);
        }
    }
    
    if ($image) {
        imagewebp($image, $destination, 85);
        imagedestroy($image);
    } elseif (!file_exists($destination)) {
        move_uploaded_file($tmpName, $destination); // Fallback
    }
    $sql = "INSERT INTO product_details 
    (product_id, image, options, value, gender, label, stock, ratings)
    VALUES 
    ('$product_id', '$imageName', '$option', '$value', '$gender', '$label', '$stock', '$ratings')";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("Location: product-details");
        exit;
    }
}

//edit product details
if (isset($_POST['edit_product_details'])) {
    $id = $_POST['id'];
    $product_id = $_POST['product_id'];
    $imageNameOld = $_POST['previous_image_hidden'];
    $option     = $_POST['option'];
    $value      = $_POST['value'];
    $gender     = $_POST['gender'];
    $label      = $_POST['label'];
    $stock      = $_POST['stock'];
    $ratings    = $_POST['ratings'];

    // Check if a new image was uploaded
    if (!empty($_FILES['image']['name'])) {
        // Upload new image (WebP Conversion)
        $originalName = $_FILES["image"]["name"];
        $tmpName      = $_FILES['image']['tmp_name'];
        $imageNameNew = time() . "_" . pathinfo($originalName, PATHINFO_FILENAME) . ".webp";
        $destination  = "./assets/images/products/" . $imageNameNew;

        $info = getimagesize($tmpName);
        $image = null;
        if ($info !== false) {
            $mime = $info['mime'];
            if ($mime == 'image/jpeg') {
                $image = imagecreatefromjpeg($tmpName);
            } elseif ($mime == 'image/png') {
                $image = imagecreatefrompng($tmpName);
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
            } elseif ($mime == 'image/webp') {
                move_uploaded_file($tmpName, $destination);
            }
        }
        
        if ($image) {
            imagewebp($image, $destination, 85);
            imagedestroy($image);
        } elseif (!file_exists($destination)) {
            move_uploaded_file($tmpName, $destination); // Fallback
        }

        // Delete old image file if it exists and is different from the new one
        if (!empty($imageNameOld) && $imageNameOld !== $imageNameNew) {
            $oldImagePath = "./assets/images/products/" . $imageNameOld;
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
    } else {
        // No new image uploaded, keep the old image name
        $imageNameNew = $imageNameOld;
    }

    $sql = "UPDATE product_details 
    SET product_id = '$product_id', image = '$imageNameNew', options = '$option', value = '$value', gender = '$gender', label = '$label', stock = '$stock', ratings = '$ratings'
    WHERE id = '$id'";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("Location: product-details");
        exit;
    }
}

// delete product details
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = (int)$_GET['id'];
    $sqlDelete = "DELETE FROM product_details WHERE id = $id";
    if (mysqli_query($conn, $sqlDelete)) {
        header("Location: product-details");
        exit;
    }
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
                <button class="btn btn-ma mt-3 me-4" data-bs-toggle="modal" data-bs-target="#addProductDetails">Add Product Details</button>
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
                                    <th>Product Name</th>
                                    <th>Image</th>
                                    <th>Options</th>
                                    <th>Values</th>
                                    <th>Gender</th>
                                    <th>Stock</th>
                                    <th>Ratings</th>
                                    <th>Labels</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="js-admin-tbody">
                                <?php
                                $sqlProductDetails = "SELECT * FROM product_details";
                                $resultProductDetails = mysqli_query($conn, $sqlProductDetails);
                                if (mysqli_num_rows($resultProductDetails) > 0) {
                                    $sr = 0;
                                    while ($rowProductDetails = mysqli_fetch_assoc($resultProductDetails)) {
                                        $sr++;

                                        $productID = $rowProductDetails['product_id'];
                                        $sqlProduct = "SELECT name FROM products WHERE id = '$productID'";
                                        $resultProduct = mysqli_query($conn, $sqlProduct);
                                        $productName = "";
                                        if (mysqli_num_rows($resultProduct) > 0) {
                                            $rowProduct = mysqli_fetch_assoc($resultProduct);
                                            $productName = $rowProduct['name'];
                                        }




                                ?>
                                        <tr class="js-admin-row" data-sr="<?php echo $sr; ?>" data-id="<?php echo $rowProductDetails['id']; ?>" data-name="<?php echo $productName; ?>">

                                            <td><?php echo $sr; ?></td>
                                            <td>PRDT-<?php echo $rowProductDetails['id']; ?></td>
                                            <td><?php echo $productName; ?></td>
                                            <td><img src="./assets/images/products/<?php echo $rowProductDetails['image']; ?>" alt="Product Image" width="70" class="rounded"></td>
                                            <td><?php echo $rowProductDetails['options']; ?></td>
                                            <td><?php echo $rowProductDetails['value']; ?></td>
                                            <td>
                                                <?php
                                                if ($rowProductDetails['gender'] == 1) {
                                                    echo "Male";
                                                } elseif ($rowProductDetails['gender'] == 2) {
                                                    echo "Female";
                                                } else {
                                                    echo "Universal";
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo $rowProductDetails['stock']; ?></td>
                                            <td><?php echo $rowProductDetails['ratings']; ?></td>
                                            <td>
                                                <?php
                                                if ($rowProductDetails['label'] == 1) {
                                                    echo '<span class="badge badge-ma rounded-pill">Normal</span>';
                                                } elseif ($rowProductDetails['label'] == 2) {
                                                    echo '<span class="badge badge-trending rounded-pill">🔥 Trending</span>';
                                                } else {
                                                    echo '<span class="badge badge-ma rounded-pill">New Drop</span>';
                                                }
                                                ?>
                                            </td>
                                            <td class="text-end">
                                                <!-- edit button -->
                                                <button class="btn btn-sm btn-ma-outline edit-btn" data-bs-toggle="modal" data-bs-target="#editProductDetails" data-id="<?php echo $rowProductDetails['id']; ?>" data-product-id="<?php echo $productID; ?>" data-name="<?php echo $productName; ?>" data-image="<?php echo $rowProductDetails['image']; ?>" data-options="<?php echo $rowProductDetails['options']; ?>" data-value="<?php echo $rowProductDetails['value']; ?>" data-gender="<?php echo $rowProductDetails['gender']; ?>" data-stock="<?php echo $rowProductDetails['stock']; ?>" data-ratings="<?php echo $rowProductDetails['ratings']; ?>" data-label="<?php echo $rowProductDetails['label']; ?>">Edit</button>
                                                <!-- delete button -->
                                                <button
                                                    class="btn btn-sm btn-ma-ghost text-danger delete-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    data-id="<?php echo $rowProductDetails['id']; ?>"
                                                    data-name="<?php echo $productName; ?>">
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
            <div class="modal fade" id="addProductDetails" tabindex="-1" aria-hidden="true">
                <form class="modal-dialog modal-dialog-centered modal-lg" method="post" id="addProductDetailsForm" enctype="multipart/form-data">
                    <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                        <div class="modal-header border-0">
                            <h5 class="modal-title text-white">Add Product Details</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label ma-muted">Product Name</label>
                                    <div class="ma-product-search-wrap">
                                        <input
                                            id="productSearchInput" class="form-control" placeholder="Search product name…" type="text" autocomplete="off" required name="product_name" />
                                        <input type="hidden" id="productIdInput" name="product_id" required />
                                        <ul id="productDropdown" class="ma-product-dropdown"></ul>
                                    </div>
                                    <!-- Hidden select keeps PHP data; JS reads from it -->
                                    <select id="productSelectHidden" class="d-none">
                                        <option value="">Select Product</option>
                                        <?php
                                        $sqlProducts = "SELECT id, name FROM products WHERE status = 1";
                                        $resultProducts = mysqli_query($conn, $sqlProducts);
                                        while ($rowProducts = mysqli_fetch_assoc($resultProducts)) {
                                        ?>
                                            <option value="<?php echo $rowProducts['id']; ?>"><?php echo htmlspecialchars($rowProducts['name']); ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>



                                </div>
                                <div class="col-md-6">
                                    <label class="form-label ma-muted">Image</label>
                                    <input class="form-control" type="file" accept="image/*" required name="image" />
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label ma-muted">Option</label>
                                    <input class="form-control" placeholder="eg: Size, Color" type="text" required name="option" />
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label ma-muted">Value</label>
                                    <input class="form-control" placeholder="eg: 40mm, Black" type="text" required name="value" />
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label ma-muted">Gender</label>
                                    <select class="form-select" required name="gender">
                                        <option disabled selected>Select Gender</option>
                                        <option value="1">Male</option>
                                        <option value="2">Female</option>
                                        <option value="3">Universal</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label ma-muted">Label</label>
                                    <select class="form-select" required name="label">
                                        <option disabled selected>Select Label</option>
                                        <option value="1">Normal</option>
                                        <option value="2">🔥 Trending</option>
                                        <option value="3">New Drop</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label ma-muted">Stock</label>
                                    <input class="form-control" placeholder="eg: 100" type="number" required name="stock" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label ma-muted">Ratings</label>
                                    <input class="form-control" placeholder="eg: 4.5" type="number" step="0.1" min="0" max="5" required name="ratings" />
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <input type="hidden" name="add_product_details" value="1">
                                <button class="btn btn-ma" type="submit" id="add_product_details">Save Details</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- edit Modal -->
            <div class="modal fade" id="editProductDetails" tabindex="-1" aria-hidden="true">
                <form class="modal-dialog modal-dialog-centered modal-lg" method="post" id="editProductDetailsForm" enctype="multipart/form-data">
                    <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                        <div class="modal-header border-0">
                            <h5 class="modal-title text-white">Edit Product Details</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label ma-muted">Product Name</label>
                                    <div class="ma-product-search-wrap">
                                        <input
                                            id="editProductSearchInput" class="form-control" placeholder="Search product name…" type="text" autocomplete="off" required name="product_name" />
                                        <input type="hidden" id="editProductIdInput" name="product_id" required />
                                        <ul id="editProductDropdown" class="ma-product-dropdown"></ul>
                                    </div>
                                    <!-- Hidden select keeps PHP data; JS reads from it -->
                                    <select id="editProductSelectHidden" class="d-none">
                                        <option value="">Select Product</option>
                                        <?php
                                        $sqlProducts = "SELECT id, name FROM products WHERE status = 1";
                                        $resultProducts = mysqli_query($conn, $sqlProducts);
                                        while ($rowProducts = mysqli_fetch_assoc($resultProducts)) {
                                        ?>
                                            <option value="<?php echo $rowProducts['id']; ?>"><?php echo htmlspecialchars($rowProducts['name']); ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>



                                </div>
                                <div class="col-md-6">
                                    <label class="form-label ma-muted">Image</label>
                                    <input class="form-control" type="file" accept="image/*"  name="image" />

                                </div>
                                <div class="col-md-6">
                                    <label class="form-label ma-muted mt-2">Previous Image</label>
                                    <input type="text" name="previous_image" class="form-control" readonly>
                                    <input type="hidden" name="previous_image_hidden" class="form-control mt-3" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label ma-muted">Option</label>
                                    <input class="form-control" placeholder="eg: Size, Color" type="text" required name="option" />
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label ma-muted">Value</label>
                                    <input class="form-control" placeholder="eg: 40mm, Black" type="text" required name="value" />
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label ma-muted">Gender</label>
                                    <select class="form-select" required name="gender">
                                        <option disabled selected>Select Gender</option>
                                        <option value="1">Male</option>
                                        <option value="2">Female</option>
                                        <option value="3">Universal</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label ma-muted">Label</label>
                                    <select class="form-select" required name="label">
                                        <option disabled selected>Select Label</option>
                                        <option value="1">Normal</option>
                                        <option value="2">🔥 Trending</option>
                                        <option value="3">New Drop</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label ma-muted">Stock</label>
                                    <input class="form-control" placeholder="eg: 100" type="number" required name="stock" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label ma-muted">Ratings</label>
                                    <input class="form-control" placeholder="eg: 4.5" type="number" step="0.1" min="0" max="5" required name="ratings" />
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <input type="hidden" name="id" value="">
                                <input type="hidden" name="edit_product_details" value="1">
                                <button class="btn btn-ma" type="submit" id="edit_product_details">Edit Details</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- delete modal -->
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                        <div class="modal-header border-0">
                            <h5 class="modal-title text-white">Delete Details?</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body ma-muted">Are you sure to delete details of <span class="badge badge-trending rounded-pill text-capitalize fs-6">Details</span></div>
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
            <script src="./assets/js/product-details.js"></script>

            <script>
                (function() {
                    function initProductSearch(searchInputId, hiddenIdId, hiddenSelectId, dropdownId) {
                        const searchInput = document.getElementById(searchInputId);
                        const hiddenId = document.getElementById(hiddenIdId);
                        const hiddenSelect = document.getElementById(hiddenSelectId);
                        const dropdown = document.getElementById(dropdownId);

                        if (!searchInput || !hiddenSelect || !dropdown) return;

                        // Build array of {id, name} from the hidden select
                        const allProducts = Array.from(hiddenSelect.options)
                            .filter(o => o.value !== '')
                            .map(o => ({
                                id: o.value,
                                name: o.text
                            }));

                        function renderList(items) {
                            dropdown.innerHTML = '';
                            if (!items.length) {
                                const li = document.createElement('li');
                                li.textContent = 'No products found';
                                li.classList.add('ma-pd-empty');
                                dropdown.appendChild(li);
                            } else {
                                items.forEach(p => {
                                    const li = document.createElement('li');
                                    li.textContent = p.name;
                                    li.dataset.id = p.id;
                                    li.addEventListener('mousedown', (e) => {
                                        e.preventDefault();
                                        searchInput.value = p.name;
                                        hiddenId.value = p.id;
                                        dropdown.classList.remove('show');
                                    });
                                    dropdown.appendChild(li);
                                });
                            }
                            dropdown.classList.add('show');
                        }

                        searchInput.addEventListener('input', () => {
                            const q = searchInput.value.trim().toLowerCase();
                            hiddenId.value = ''; // clear until a choice is made
                            if (!q) {
                                dropdown.classList.remove('show');
                                return;
                            }
                            renderList(allProducts.filter(p => p.name.toLowerCase().includes(q)));
                        });

                        searchInput.addEventListener('focus', () => {
                            if (searchInput.value.trim()) {
                                const q = searchInput.value.trim().toLowerCase();
                                renderList(allProducts.filter(p => p.name.toLowerCase().includes(q)));
                            }
                        });

                        // Close dropdown when clicking elsewhere
                        document.addEventListener('click', (e) => {
                            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                                dropdown.classList.remove('show');
                            }
                        });
                    }

                    initProductSearch('productSearchInput', 'productIdInput', 'productSelectHidden', 'productDropdown');
                    initProductSearch('editProductSearchInput', 'editProductIdInput', 'editProductSelectHidden', 'editProductDropdown');
                })();
            </script>

</body>

</html>