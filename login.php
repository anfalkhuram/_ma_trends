<?php
$pageTitle = "Login or Create Account | MATrends";
$description = "Sign in to your MATrends account or create a new one to start shopping trending rings, watches, bags and accessories.";
$keywords = "MATrends login, create account, sign in, register MATrends";
$author = "MATrends";
$robots = "noindex, nofollow";
$ogTitle = "Login | MATrends";
$ogDescription = "Sign in or create a MATrends account to buy trending accessories.";
$ogType = "website";
$ogUrl = "https://www.matrends.store/login";

require_once('inc/top.php');

// --- Handle POST (login / register) ---
$loginError = '';
$registerError = '';
$registerSuccess = '';

if (isset($_GET['reset']) && $_GET['reset'] === 'success') {
    $registerSuccess = 'Password reset successfully. You can now log in.';
}

// Capture and WHITELIST the return URL to prevent open redirect
$allowedRedirects = ['cart', 'checkout', 'index', 'shop', 'profile', 'products'];
$rawRedirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';
$redirect = in_array($rawRedirect, $allowedRedirects) ? $rawRedirect : '';

// If already logged in, bounce away
if (isLoggedIn()) {
    if (isAdminLoggedIn()) {
        $goto = 'admins/dashboard';
    } else {
        $goto = $redirect ? $redirect : 'index';
    }
    session_write_close();
    header("Location: " . $goto);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- LOGIN ---
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            $loginError = 'Please fill in all fields.';
        } else {
            $safeEmail = mysqli_real_escape_string($conn, $email);
            $sqlUser = "SELECT * FROM users WHERE email = '$safeEmail' LIMIT 1";
            $resultUser = mysqli_query($conn, $sqlUser);

            if ($resultUser && mysqli_num_rows($resultUser) > 0) {
                $user = mysqli_fetch_assoc($resultUser);

                if (password_verify($password, $user['password'])) {
                    require_once('inc/otp_functions.php');
                    try {
                        $otpSent = storeAndSendOTP($conn, $user['email'], $user['status'] == 'admin' ? 'admin' : 'user', 'login_verification');
                        
                        if ($otpSent) {
                            // Store user data temporarily
                            $_SESSION['temp_user'] = [
                                'id'    => $user['id'],
                                'name'  => $user['name'],
                                'email' => $user['email'],
                                'status'=> $user['status']
                            ];
                            
                            $redirectParam = $redirect ? '&redirect=' . urlencode($redirect) : '';
                            header("Location: verify_otp.php?purpose=login_verification" . $redirectParam);
                            exit();
                        } else {
                            $loginError = 'Could not send OTP. Please ensure database tables and email settings are configured on the live server.';
                        }
                    } catch (Throwable $e) {
                        $loginError = 'System error: ' . $e->getMessage() . '. Did you forget to upload vendor folder or run the database SQL?';
                    }
                } else {
                    $loginError = 'Incorrect email or password.';
                }
            } else {
                $loginError = 'Incorrect email or password.';
            }
        }
    }
}
?>

<body class="d-flex align-items-center" style="min-height:100vh;">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <?php if ($loginError): ?>
                <script>
                    window.onload = function() {
                        const toastMsg = document.getElementById('errorToastMessage');
                        if (toastMsg) {
                            toastMsg.innerText = "<?php echo addslashes($loginError); ?>";
                            const toastTrigger = document.getElementById('errorToast');
                            const toast = new bootstrap.Toast(toastTrigger);
                            toast.show();
                        } else {
                            alert("<?php echo addslashes($loginError); ?>");
                        }
                    }
                </script>
            <?php endif; ?>
            <?php if ($registerSuccess): ?>
                <div class="alert alert-success text-center">
                    <?php echo htmlspecialchars($registerSuccess); ?>
                </div>
            <?php endif; ?>
            <div class="ma-card p-4 p-md-5 ma-shadow">
                <div class="text-center">
                    <a href="index" class="text-decoration-none d-inline-flex align-items-center">
                    <img src="./assets/img/ma_trends_ill.webp" alt="" width="80" loading="lazy" class="ma-pill">    
                    
                    <!-- <span class="fw-bold text-white mt-3">Trends</span> -->
                    </a>
                    <div class="ma-muted">What’s Trending Now</div>
                </div>
                <h1 class="h4 fw-bold mb-3 text-center">Login</h1>

                <form method="POST" action="login<?php echo $redirect ? '?redirect=' . urlencode($redirect) : ''; ?>">
              <input type="hidden" name="action" value="login" />
              <div class="mb-3">
                <label class="form-label ma-muted">Email</label>
                <input class="form-control" type="email" name="email" placeholder="you@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required />
              </div>
              <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <label class="form-label ma-muted mb-0">Password</label>
                  <a href="forgot_password.php" class="text-decoration-none small text-ma">Forgot Password?</a>
                </div>
                <div class="position-relative">
                  <input class="form-control toggle-password-field pe-5" type="password" name="password" placeholder="••••••••" required />
                  <button type="button" class="btn border-0 position-absolute end-0 top-50 translate-middle-y px-3 js-password-toggle" tabindex="-1">
                      <i class="fas fa-eye text-white"></i>
                  </button>
                </div>
              </div>
              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="remember" />
                <label class="form-check-label ma-muted" for="remember">Remember me</label>
              </div>
              <button type="submit" class="btn btn-ma w-100">Login</button>
            </form>
            <div class="text-center mt-3">
              <span class="ma-muted small">No account?</span>
              <a class="text-decoration-none" href="signup">Sign up</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php require_once('inc/bottom.php'); ?>
    <script src="assets/js/password-toggle.js"></script>
</body>
</html>
