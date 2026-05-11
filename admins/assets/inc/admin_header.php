<header
    class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 p-md-4 border-bottom ma-border"
    style="position: sticky; top: 0; z-index: 1030; background: rgba(11,12,15,.85); backdrop-filter: blur(10px);">
    <div>
        <div class="ma-kicker mb-1">Admin</div>
        <h1 class="h4 fw-bold mb-0"><?php echo $pageName;?></h1>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="ma-pill small">Hi, <?php echo htmlspecialchars($_SESSION['admin']['name']); ?></span>
        <a class="btn btn-ma-outline btn-sm" href="../logout">Sign out</a>
    </div>
</header>