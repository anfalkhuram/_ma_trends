 <?php
    $sqlCategories = "SELECT * FROM categories where status = 1";
    $resultCategories = mysqli_query($conn, $sqlCategories);

    $cartCount = 0;
    if (isLoggedIn()) {
        $userId = $_SESSION['user']['id'] ?? $_SESSION['admin']['id'];
        $sqlCartCount = "SELECT COUNT(id) as total FROM cart WHERE user_id = $userId";
        $resCartCount = mysqli_query($conn, $sqlCartCount);
        if ($resCartCount) {
            $rowCartCount = mysqli_fetch_assoc($resCartCount);
            $cartCount = $rowCartCount['total'] ?? 0;
        }
    }
    ?>


 <nav class="navbar navbar-expand-lg navbar-dark fixed-top ma-navbar">
     <div class="container">
         <a class="navbar-brand d-flex align-items-center gap-2" href="index.html">
             <span class="ma-pill color-soft-gold fs-4"><img src="assets/img/ma_trends_ill.png" alt="logo..." width="40"> <span class="ma-trends-logo-text">Trends</span></span>

             <span class="d-none d-md-inline ma-muted fw-semibold mt-3">What&rsquo;s Trending
                 Now</span>
         </a>
         <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse"
             data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Toggle navigation">
             <span class="navbar-toggler-icon"></span>
         </button>

         <div class="collapse navbar-collapse" id="navMain">
             <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                 <li class="nav-item"><a class="nav-link ma-hover-underline <?php  echo($pageName == 'Home')?'active':'';?>" href="index">Home</a></li>
                 <li class="nav-item"><a class="nav-link ma-hover-underline <?php  echo($pageName == 'Shop')?'active':'';?>" href="shop">Shop</a></li>
                 <li class="nav-item dropdown ma-hover-underline">
                     <a class="nav-link dropdown-toggle 
                     <?php 
                     echo($pageName == 'Category Watches'  || $pageName == 'Category Bags' || $pageName == 'Category Jewelry' || $pageName == 'Category Sun Glasses' || $pageName == 'Category Couple Collection' || $pageName == 'Category Accessories')?'active':'';
                     ?>" href="#" role="button" data-bs-toggle="dropdown"
                         aria-expanded="false">Categories</a>
                     <ul class="dropdown-menu dropdown-menu-dark ma-bg-surface-2 border ma-border">
                         <?php
                            if (mysqli_num_rows($resultCategories) > 0) {
                                while ($rowCategory = mysqli_fetch_assoc($resultCategories)) {
                            ?>
                                 <li><a class="dropdown-item text-capitalize <?php 
                                    echo($pageName == 'Category '.ucwords($rowCategory['name'])?'active':'');
                                 ?>" href=".<?php echo $rowCategory['slug']; ?>?id=<?php echo $rowCategory['id']; ?>"><?php echo $rowCategory['name']; ?></a></li>

                         <?php
                                }
                            }

                            ?>

                     </ul>
                 </li>
                  <?php if (isLoggedIn()): ?>
                      <li class="nav-item mt-1 mt-lg-0">
                          <a class="nav-link ma-muted d-flex align-items-center" href="<?php echo isAdminLoggedIn() ? 'admins/dashboard' : 'profile'; ?>">
                              <?php 
                                $fullName = isAdminLoggedIn() ? $_SESSION['admin']['name'] : $_SESSION['user']['name'];
                                $words = explode(" ", trim($fullName));
                                $initials = "";
                                $count = 0;
                                foreach ($words as $w) {
                                    if (!empty($w) && $count < 2) {
                                        $initials .= strtoupper(substr($w, 0, 1));
                                        $count++;
                                    }
                                }
                              ?>
                              <div class="ma-nav-avatar"><?php echo $initials; ?></div>
                              <?php 
                                if (isAdminLoggedIn()) {
                                    echo htmlspecialchars($_SESSION['admin']['name']) . ' (Admin)';
                                } else {
                                    echo htmlspecialchars($_SESSION['user']['name']);
                                }
                              ?>
                          </a>
                      </li>
                      <li class="nav-item ms-lg-2 mt-2 mt-lg-0 position-relative">
                          <a class="btn btn-ma-outline btn-sm w-100 w-lg-auto" href="cart">Cart</a>
                          <span class="position-absolute translate-middle badge rounded-pill bg-danger js-cart-count custom-positioning"><?php echo $cartCount; ?></span>
                      </li>
                      <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                          <a class="btn btn-ma-outline btn-sm w-100 w-lg-auto js-logout" href="logout">Logout</a>
                      </li>
                  <?php else: ?>
                      <li class="nav-item">
                          <a class="nav-link ma-hover-underline <?php echo ($pageName == 'Login') ? 'active' : ''; ?>" href="login">Login</a>
                      </li>
                      <li class="nav-item ms-lg-2 mt-2 mt-lg-0 position-relative">
                          <a class="btn btn-ma-outline btn-sm w-100 w-lg-auto js-guest-cart" href="login?redirect=cart">Cart</a>
                      </li>
                  <?php endif; ?>
             </ul>
         </div>
     </div>
 </nav>