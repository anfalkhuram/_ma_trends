<?php
$pageTitle = "Create Account | MATrends";
$description = "Create a new MATrends account to start shopping trending rings, watches, bags and accessories.";
$keywords = "MATrends register, create account, signup MATrends";
$author = "MATrends";
$robots = "noindex, nofollow";
$ogTitle = "Sign Up | MATrends";
$ogDescription = "Create a MATrends account to buy trending accessories.";
$ogType = "website";
$ogUrl = "https://www.matrends.store/signup";

require_once('inc/top.php');

// --- Handle POST (register) ---
$registerError = '';
// Whitelist redirect to prevent open redirect attacks
$allowedRedirects = ['cart', 'checkout', 'index', 'shop', 'profile', 'products'];
$rawRedirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';
$redirect = in_array($rawRedirect, $allowedRedirects) ? $rawRedirect : '';

// If already logged in, bounce away
if (isLoggedIn()) {
    $goto = $redirect ? $redirect : 'index';
    session_write_close();
    header("Location: " . $goto);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'register') {
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['reg_email'] ?? '');
        $password = $_POST['reg_password'] ?? '';
        $confirm  = $_POST['reg_confirm'] ?? '';

        if (!$name || !$email || !$password || !$confirm) {
            $registerError = 'Please fill in all fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $registerError = 'Please enter a valid email address.';
        } elseif (strlen($password) < 8) {
            $registerError = 'Password must be at least 8 characters.';
        } elseif ($password !== $confirm) {
            $registerError = 'Passwords do not match.';
        } else {
            $safeName  = mysqli_real_escape_string($conn, $name);
            $safeEmail = mysqli_real_escape_string($conn, $email);

            // Check duplicate email
            $checkSql = "SELECT id FROM users WHERE email = '$safeEmail' LIMIT 1";
            $checkRes = mysqli_query($conn, $checkSql);

            if ($checkRes && mysqli_num_rows($checkRes) > 0) {
                $registerError = 'An account with this email already exists. Please log in.';
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $insertSql = "INSERT INTO users (name, email, password, status, created_at)
                              VALUES ('$safeName', '$safeEmail', '$hashed', 'user', NOW())";
                if (mysqli_query($conn, $insertSql)) {
                    // Send OTP
                    require_once('inc/otp_functions.php');
                    storeAndSendOTP($conn, $safeEmail, 'user', 'signup_verification');

                    // Redirect to verification page
                    $redirectParam = $redirect ? '&redirect=' . urlencode($redirect) : '';
                    header("Location: verify_otp.php?email=" . urlencode($email) . "&purpose=signup_verification" . $redirectParam);
                    exit();
                } else {
                    $registerError = 'Something went wrong. Please try again.';
                }
            }
        }
    }
}
?>

<body class="d-flex align-items-center" style="min-height:100vh;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                <?php if ($registerError): ?>
                    <script>
                        window.onload = function() {
                            const toastMsg = document.getElementById('errorToastMessage');
                            toastMsg.innerText = "<?php echo addslashes($registerError); ?>";
                            const toastTrigger = document.getElementById('errorToast');
                            const toast = new bootstrap.Toast(toastTrigger);
                            toast.show();
                        }
                    </script>
                <?php endif; ?>
                <div class="ma-card p-4 p-md-5 ma-shadow">
                    <div class="text-center mb-4">
                        <a href="index" class="text-decoration-none d-inline-flex align-items-center gap-2">
                            <img src="./assets/img/ma_trends_ill.png" alt="" width="80" loading="lazy" class="ma-pill">
                        </a>
                        <div class="ma-muted">Join the Community</div>
                    </div>
                    <h1 class="h4 fw-bold mb-3 text-center">Create Account</h1>

                    <form method="POST" action="signup<?php echo $redirect ? '?redirect=' . urlencode($redirect) : ''; ?>">
                        <input type="hidden" name="action" value="register" />

                        <div class="mb-3">
                            <label class="form-label ma-muted">Full Name</label>
                            <input class="form-control" type="text" name="name" placeholder="John Doe" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label ma-muted">Email Address</label>
                            <input class="form-control" type="email" name="reg_email" placeholder="you@example.com" value="<?php echo htmlspecialchars($_POST['reg_email'] ?? ''); ?>" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label ma-muted">Password</label>
                            <div class="position-relative">
                                <input class="form-control toggle-password-field pe-5" type="password" name="reg_password" placeholder="••••••••" required />
                                <button type="button" class="btn border-0 position-absolute end-0 top-50 translate-middle-y px-3 js-password-toggle" tabindex="-1">
                                    <i class="fas fa-eye text-white"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label ma-muted">Confirm Password</label>
                            <div class="position-relative">
                                <input class="form-control toggle-password-field pe-5" type="password" name="reg_confirm" placeholder="••••••••" required />
                                <button type="button" class="btn border-0 position-absolute end-0 top-50 translate-middle-y px-3 js-password-toggle" tabindex="-1">
                                    <i class="fas fa-eye text-white"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-ma w-100">Sign Up</button>
                    </form>

                    <div class="text-center mt-3">
                        <span class="ma-muted small">Already have an account?</span>
                        <a class="text-decoration-none" href="login">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once('inc/bottom.php'); ?>
    <script src="assets/js/password-toggle.js"></script>
</body>

</html>