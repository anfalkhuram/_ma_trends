<?php
$pageTitle = "Reset Password | MATrends";
$description = "Reset your MATrends account password.";
$keywords = "MATrends reset password";
$author = "MATrends";
$robots = "noindex, nofollow";
$ogTitle = "Reset Password | MATrends";
$ogDescription = "Reset your MATrends account password.";
$ogType = "website";
$ogUrl = "https://www.matrends.store/reset_password";

require_once('inc/top.php');

$error = '';
$success = '';

if (!isset($_SESSION['forgot_password_verified'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['forgot_password_verified'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_password') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    
    if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $safeEmail = mysqli_real_escape_string($conn, $email);
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE users SET password = '$hashed' WHERE email = '$safeEmail'";
        if (mysqli_query($conn, $sql)) {
            // Fetch user name
            $resName = mysqli_query($conn, "SELECT name FROM users WHERE email = '$safeEmail' LIMIT 1");
            $userName = "User";
            if ($resName && mysqli_num_rows($resName) > 0) {
                $row = mysqli_fetch_assoc($resName);
                $userName = $row['name'];
            }
            
            require_once('inc/email_functions.php');
            $loginUrl = "http" . (isset($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . "/login.php";
            $data = [
                'name' => $userName,
                'loginUrl' => $loginUrl
            ];
            sendTransactionalEmail($email, "Your MATrends Password Was Reset", 'password_reset_success', $data, 'user');
            
            unset($_SESSION['forgot_password_verified']);
            // Pass a flag to login page to show success toast
            header("Location: login.php?reset=success");
            exit();
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<body class="d-flex align-items-center" style="min-height:100vh;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                <?php if ($error): ?>
                    <script>
                        window.onload = function() {
                            const toastMsg = document.getElementById('errorToastMessage');
                            if(toastMsg) {
                                toastMsg.innerText = "<?php echo addslashes($error); ?>";
                                const toastTrigger = document.getElementById('errorToast');
                                const toast = new bootstrap.Toast(toastTrigger);
                                toast.show();
                            } else {
                                alert("<?php echo addslashes($error); ?>");
                            }
                        }
                    </script>
                <?php endif; ?>

                <div class="ma-card p-4 p-md-5 ma-shadow">
                    <div class="text-center mb-4">
                        <img src="./assets/img/ma_trends_ill.webp" alt="" width="80" loading="lazy" class="ma-pill">
                        <h1 class="h4 fw-bold mt-3 text-center">Reset Password</h1>
                        <p class="ma-muted small">Enter your new password below.</p>
                    </div>

                    <form method="POST" action="">
                        <input type="hidden" name="action" value="reset_password" />
                        
                        <div class="mb-3">
                            <label class="form-label ma-muted">New Password</label>
                            <div class="position-relative">
                                <input class="form-control toggle-password-field pe-5" type="password" name="password" placeholder="••••••••" required />
                                <button type="button" class="btn border-0 position-absolute end-0 top-50 translate-middle-y px-3 js-password-toggle" tabindex="-1">
                                    <i class="fas fa-eye text-white"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label ma-muted">Confirm Password</label>
                            <div class="position-relative">
                                <input class="form-control toggle-password-field pe-5" type="password" name="confirm" placeholder="••••••••" required />
                                <button type="button" class="btn border-0 position-absolute end-0 top-50 translate-middle-y px-3 js-password-toggle" tabindex="-1">
                                    <i class="fas fa-eye text-white"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-ma w-100">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php require_once('inc/bottom.php'); ?>
    <script src="assets/js/password-toggle.js"></script>
</body>
</html>
