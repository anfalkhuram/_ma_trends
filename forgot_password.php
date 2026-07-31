<?php
$pageTitle = "Forgot Password | MATrends";
$description = "Reset your MATrends account password.";
$keywords = "MATrends forgot password, reset password";
$author = "MATrends";
$robots = "noindex, nofollow";
$ogTitle = "Forgot Password | MATrends";
$ogDescription = "Reset your MATrends account password.";
$ogType = "website";
$ogUrl = "https://www.matrends.store/forgot_password";

require_once('inc/top.php');
require_once('inc/otp_functions.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'forgot_password') {
    $email = trim($_POST['email'] ?? '');
    
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $safeEmail = mysqli_real_escape_string($conn, $email);
        $resultUser = mysqli_query($conn, "SELECT status FROM users WHERE email = '$safeEmail' LIMIT 1");
        
        // For security, do not reveal whether the email exists. Show a general message.
        // But behind the scenes, only send OTP if it exists.
        if ($resultUser && mysqli_num_rows($resultUser) > 0) {
            $userRow = mysqli_fetch_assoc($resultUser);
            $role = ($userRow['status'] === 'admin') ? 'admin' : 'user';
            storeAndSendOTP($conn, $email, $role, 'forgot_password');
        }
        
        $_SESSION['forgot_password_email'] = $email;
        header("Location: verify_otp.php?purpose=forgot_password");
        exit();
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
                            }
                        }
                    </script>
                <?php endif; ?>

                <div class="ma-card p-4 p-md-5 ma-shadow">
                    <div class="text-center mb-4">
                        <img src="./assets/img/ma_trends_ill.webp" alt="" width="80" loading="lazy" class="ma-pill">
                        <h1 class="h4 fw-bold mt-3 text-center">Forgot Password</h1>
                        <p class="ma-muted small">Enter your email address and we'll send you a 6-digit code to reset your password.</p>
                    </div>

                    <form method="POST" action="">
                        <input type="hidden" name="action" value="forgot_password" />
                        
                        <div class="mb-4">
                            <label class="form-label ma-muted">Email Address</label>
                            <input class="form-control" type="email" name="email" placeholder="you@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required />
                        </div>

                        <button type="submit" class="btn btn-ma w-100">Send Reset Code</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="login.php" class="text-decoration-none small">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once('inc/bottom.php'); ?>
</body>
</html>
