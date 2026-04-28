<?php
require_once('./assets/inc/admin_top.php');
?>

<body>
    <div class="ma-admin-shell d-flex flex-column flex-md-row ma-admin-collapsed">
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

           
                
 

    <?php
    require_once('./assets/inc/admin_bottom.php');
    ?>
</body>

</html>