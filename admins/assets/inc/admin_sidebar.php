  <aside class="ma-sidebar p-3 p-md-4 d-none d-md-block">
      <div class="d-flex align-items-center mb-4">
        <span class=" h6"><img src="./assets/images/ma_trends_ill.webp" alt="..." width="50" height="50"></span>
        
      </div>
      <nav class="nav flex-column gap-1 ma-sidebar-nav">
        <a class="nav-link <?php echo ($pageName == 'Dashboard') ? "active" : ""; ?>" href="../admins/dashboard" data-bs-toggle="tooltip" data-bs-placement="right"  title="Dashboard">
          <span class="ma-sidebar-icon"><i class="fa fa-gauge-high"></i></span>
          <span class="ma-sidebar-label">Dashboard</span>
        </a>
        <a class="nav-link <?php echo ($pageName == 'Categories') ? "active" : ""; ?>" href="../admins/categories" data-bs-toggle="tooltip" data-bs-placement="right"  title="Categories">
          <span class="ma-sidebar-icon"><i class="fa-solid fa-tags"></i></span>
          <span class="ma-sidebar-label">Categories</span>
        </a>
        
        <a class="nav-link <?php echo ($pageName == 'Products') ? "active" : ""; ?>" href="../admins/products" data-bs-toggle="tooltip" data-bs-placement="right"  title="Products">
          <span class="ma-sidebar-icon"><i class="fa-solid fa-box"></i></span>
          <span class="ma-sidebar-label">Products</span>
        </a>
        <a class="nav-link <?php echo ($pageName == 'Product Details') ? "active" : ""; ?>" href="../admins/product-details" data-bs-toggle="tooltip" data-bs-placement="right"  title="Product Details">
          <span class="ma-sidebar-icon"><i class="fa-solid fa-circle-info"></i></span>
          <span class="ma-sidebar-label">Product Details</span>
        </a>
        <a class="nav-link <?php echo ($pageName == 'Product Feedback') ? "active" : ""; ?>" href="../admins/product-feedback" data-bs-toggle="tooltip" data-bs-placement="right"  title="Product Feedback">
          <span class="ma-sidebar-icon"><i class="fa-solid fa-message"></i></span>
          <span class="ma-sidebar-label">Product Feedback</span>
        </a>
        <a class="nav-link <?php echo ($pageName == 'Orders') ? "active" : ""; ?>" href="../admins/orders" data-bs-toggle="tooltip" data-bs-placement="right"  title="Orders">
          <span class="ma-sidebar-icon"><i class="fa-solid fa-truck"></i></span>
          <span class="ma-sidebar-label">Orders</span>
        </a>
        <a class="nav-link <?php echo ($pageName == 'Roles') ? "active" : ""; ?>" href="../admins/roles" data-bs-toggle="tooltip" data-bs-placement="right" title="Roles">
          <span class="ma-sidebar-icon"><i class="fa-solid fa-user-shield"></i></span>
          <span class="ma-sidebar-label">Roles</span>
        </a>
        <a class="nav-link <?php echo ($pageName == 'Users') ? "active" : ""; ?>" href="../admins/users" data-bs-toggle="tooltip" data-bs-placement="right" title="Users">
          <span class="ma-sidebar-icon"><i class="fa-solid fa-users"></i></span>
          <span class="ma-sidebar-label">Users</span>
        </a>
        <!-- <a class="nav-link <?php echo ($pageName == 'Contacts') ? "active" : ""; ?>" href="../admins/contacts" data-bs-toggle="tooltip" data-bs-placement="right" title="Contacts">
          <span class="ma-sidebar-icon"><i class="fa-solid fa-envelope"></i></span>
          <span class="ma-sidebar-label">Contacts</span>
        </a> -->
        <a class="nav-link" href="../index" data-bs-toggle="tooltip" data-bs-placement="right" title="Visit Website" target="_blank">
          <span class="ma-sidebar-icon"><i class="fa-solid fa-arrow-up-right-from-square"></i></span>
          <span class="ma-sidebar-label">View site</span>
        </a>
        <!-- <a class="nav-link ma-muted" href="login.html">
          <span class="ma-sidebar-icon"><i class="fa-solid fa-power-off"></i></span>
          <span class="ma-sidebar-label">Logout</span>
        </a> -->
      </nav>
    </aside>