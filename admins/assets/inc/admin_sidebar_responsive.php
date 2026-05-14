<div class="d-md-none border-bottom ma-border px-3 py-2" style="position: relative; z-index: 1040;">
    <div class="dropdown w-100">
        <button class="btn btn-ma-outline w-100 d-flex justify-content-between align-items-center" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <span>Admin menu</span>
            <span class="small ma-muted"><?php echo $pageName;?></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-dark w-100 mt-2">
            <li><a class="dropdown-item <?php echo ($pageName == "Dashboard")? "active" : ""; ?>" href="../admins/dashboard">Dashboard</a></li>
            <li><a class="dropdown-item <?php echo ($pageName == "Categories")? "active" : ""; ?>" href="../admins/categories">Categories</a></li>
            <!-- <li><a class="dropdown-item <?php echo ($pageName == "Sub Categories")? "active" : ""; ?>" href="../admins/sub-categories">Sub Categories</a></li> -->
            <li><a class="dropdown-item <?php echo ($pageName == "Products")? "active" : ""; ?>" href="../admins/products">Products</a></li>
            <li><a class="dropdown-item <?php echo ($pageName == "Product Details")? "active" : ""; ?>" href="../admins/product-details">Products Details</a></li>
            <li><a class="dropdown-item <?php echo ($pageName == "Product Feedback")? "active" : ""; ?>" href="../admins/product-feedback">Products Details</a></li>
            <li><a class="dropdown-item <?php echo ($pageName == "Orders")? "active" : ""; ?>" href="../admins/orders">Orders</a></li>
            <li><a class="dropdown-item <?php echo ($pageName == "Roles")? "active" : ""; ?>" href="../admins/roles">Roles</a></li>
            <li><a class="dropdown-item <?php echo ($pageName == "Users")? "active" : ""; ?>" href="../admins/users">Users</a></li>
            <!-- <li><a class="dropdown-item <?php echo ($pageName == "Contacts")? "active" : "";?>" href="../admins/contacts">Contacts</a></li> -->
            <li>
                <hr class="dropdown-divider" />
            </li>
            <li><a class="dropdown-item" href="../index" target="_blank">View site</a></li>
            <!-- <li><a class="dropdown-item" href="login.html">Logout</a></li> -->
        </ul>
    </div>
</div>