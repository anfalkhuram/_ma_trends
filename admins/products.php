<?php
require_once('./assets/inc/admin_top.php');
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
            <div class="p-3 p-md-4">
                <div class="ma-card p-3 p-md-4">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Trending</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Minimal Gold Watch</td>
                                    <td>Watches</td>
                                    <td>$79</td>
                                    <td><span class="badge badge-ma rounded-pill">Active</span></td>
                                    <td><span class="badge badge-trending rounded-pill">🔥</span></td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-ma-outline" href="#" data-bs-toggle="modal" data-bs-target="#editProduct">Edit</a>
                                        <a class="btn btn-sm btn-ma-ghost text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Beige Mini Bag</td>
                                    <td>Bags</td>
                                    <td>$49</td>
                                    <td><span class="badge badge-ma rounded-pill">Active</span></td>
                                    <td><span class="badge badge-trending rounded-pill">🔥</span></td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-ma-outline" href="#" data-bs-toggle="modal" data-bs-target="#editProduct">Edit</a>
                                        <a class="btn btn-sm btn-ma-ghost text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Minimal Ring Set</td>
                                    <td>Jewelry</td>
                                    <td>$24</td>
                                    <td><span class="badge badge-ma rounded-pill">Active</span></td>
                                    <td><span class="badge badge-newdrop rounded-pill">New</span></td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-ma-outline" href="#" data-bs-toggle="modal" data-bs-target="#editProduct">Edit</a>
                                        <a class="btn btn-sm btn-ma-ghost text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>





            <?php
            require_once('./assets/inc/admin_bottom.php');
            ?>
</body>

</html>