 <?php
    $sqlCategories = "SELECT * FROM categories where status = 1";
    $resultCategories = mysqli_query($conn, $sqlCategories);
    ?>


 <nav class="navbar navbar-expand-lg fixed-top ma-navbar">
     <div class="container">
         <a class="navbar-brand d-flex align-items-center gap-2" href="index.html">
             <span class="ma-pill color-soft-gold fs-4"><img src="assets/img/ma_trends_ill.png" alt="logo..." width="40"> <span class="h5">Trends</span></span>

             <span class="d-none d-md-inline ma-muted fw-semibold mt-3" style="letter-spacing:.06em;">What&rsquo;s Trending
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
                 <li class="nav-item"><a class="nav-link ma-hover-underline" href="pages/login.html">Login</a></li>
                 <li class="nav-item ms-lg-2 position-relative">
                     <a class="btn btn-ma-outline btn-sm " href="cart">Cart</a>
                     <span class="cart-count fw-bold">3</span>
                 </li>
                 <li class="nav-item ms-lg-2">
                     <a class="btn btn-ma-outline btn-sm" href="pages/cart.html">Orders</a>
                 </li>
             </ul>
         </div>
     </div>
 </nav>