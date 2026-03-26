  <aside class="ma-sidebar p-3 p-md-4 d-none d-md-block">
      <div class="d-flex align-items-center gap-2 mb-4">
        <span class="ma-pill h6"><img src="../admins/assets/images/ma_trends_ill.png" alt="..." width="25" height="25"></span>
        <div class="ma-sidebar-header-text mb-2">
          <div class="fw-bold">Admin</div>
          <div class="ma-muted custom-font-size">MA Trends</div>
        </div>
        <button class="btn btn-ma-ghost btn-sm margin-left js-toggle-sidebar " type="button"
          aria-label="Toggle sidebar">
          <i class="fas fa-bars"></i>
        </button>
      </div>
      <nav class="nav flex-column gap-1 ma-sidebar-nav">
        <a class="nav-link <?php echo ($pageName == 'Dashboard') ? "active" : ""; ?>" href="../admins/dashboard">
          <span class="ma-sidebar-icon"><i class="fa fa-gauge-high"></i></span>
          <span class="ma-sidebar-label">Dashboard</span>
        </a>
        <a class="nav-link <?php echo ($pageName == 'Categories') ? "active" : ""; ?>" href="../admins/categories">
          <span class="ma-sidebar-icon"><i class="fa-solid fa-tags"></i></span>
          <span class="ma-sidebar-label">Categories</span>
        </a>
        <a class="nav-link <?php echo ($pageName == 'Sub Categories') ? "active" : ""; ?>" href="../admins/sub-categories">
          <span class="ma-sidebar-icon"><i class="fa-solid fa-sitemap"></i></span>
          <span class="ma-sidebar-label">Sub Categories</span>
        </a>
        <a class="nav-link <?php echo ($pageName == 'Products') ? "active" : ""; ?>" href="../admins/products">
          <span class="ma-sidebar-icon"><i class="fa-solid fa-box"></i></span>
          <span class="ma-sidebar-label">Products</span>
        </a>
        <a class="nav-link <?php echo ($pageName == 'Orders') ? "active" : ""; ?>" href="../admins/orders">
          <span class="ma-sidebar-icon"><i class="fa-solid fa-truck"></i></span>
          <span class="ma-sidebar-label">Orders</span>
        </a>
        <a class="nav-link <?php echo ($pageName == 'Roles') ? "active" : ""; ?>" href="../admins/roles">
          <span class="ma-sidebar-icon"><i class="fa-solid fa-user-shield"></i></span>
          <span class="ma-sidebar-label">Roles</span>
        </a>
        <a class="nav-link <?php echo ($pageName == 'Users') ? "active" : ""; ?>" href="../admins/users">
          <span class="ma-sidebar-icon"><i class="fa-solid fa-users"></i></span>
          <span class="ma-sidebar-label">Users</span>
        </a>
        <a class="nav-link <?php echo ($pageName == 'Contacts') ? "active" : ""; ?>" href="../admins/contacts">
          <span class="ma-sidebar-icon"><i class="fa-solid fa-envelope"></i></span>
          <span class="ma-sidebar-label">Contacts</span>
        </a>
        <a class="nav-link" href="../index.html">
          <span class="ma-sidebar-icon"><i class="fa-solid fa-arrow-up-right-from-square"></i></span>
          <span class="ma-sidebar-label">View site</span>
        </a>
        <!-- <a class="nav-link ma-muted" href="login.html">
          <span class="ma-sidebar-icon"><i class="fa-solid fa-power-off"></i></span>
          <span class="ma-sidebar-label">Logout</span>
        </a> -->
      </nav>
    </aside>