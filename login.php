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

// Capture the return URL (e.g. ?redirect=cart)
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';

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
                    if ($user['status'] == 'admin') {
                        $_SESSION['admin'] = [
                            'id' => $user['id'],
                            'name' => $user['name'],
                            'email' => $user['email']
                        ];
                        session_write_close();
                        header("Location: admins/dashboard");
                        exit();
                    } else {
                        $_SESSION['user'] = [
                            'id' => $user['id'],
                            'name' => $user['name'],
                            'email' => $user['email']
                        ];
                        $goto = $redirect ? $redirect : 'index';
                        session_write_close();
                        header("Location: " . $goto);
                        exit();
                    }
                } else {
                    $loginError = 'Incorrect password.';
                }
            } else {
                $loginError = 'No account found with that email.';
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
                        toastMsg.innerText = "<?php echo addslashes($loginError); ?>";
                        const toastTrigger = document.getElementById('errorToast');
                        const toast = new bootstrap.Toast(toastTrigger);
                        toast.show();
                    }
                </script>
            <?php endif; ?>
            <div class="ma-card p-4 p-md-5 ma-shadow">
                <div class="text-center">
                    <a href="index" class="text-decoration-none d-inline-flex align-items-center">
                    <img src="./assets/img/ma_trends_ill.png" alt="" width="80" loading="lazy" class="ma-pill">    
                    
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
                <div class="d-flex justify-content-between align-items-center">
                  <label class="form-label ma-muted">Password</label>
                  
                </div>
                <input class="form-control" type="password" name="password" placeholder="••••••••" required />
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
</body>
</html>
