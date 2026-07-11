<?php
$pageTitle = "Verify OTP | MATrends";
$description = "Verify your OTP to continue.";
$keywords = "MATrends OTP, verification";
$author = "MATrends";
$robots = "noindex, nofollow";
$ogTitle = "Verify OTP | MATrends";
$ogDescription = "Verify your OTP.";
$ogType = "website";
$ogUrl = "https://www.matrends.store/verify_otp";

require_once('inc/top.php');
require_once('inc/otp_functions.php');

$error = '';
$success = '';

$purpose = $_GET['purpose'] ?? '';
$email = $_GET['email'] ?? '';

if ($purpose === 'login_verification' && isset($_SESSION['temp_user'])) {
    $email = $_SESSION['temp_user']['email'];
}

if (!$email || !$purpose) {
    header("Location: index.php");
    exit();
}

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'verify') {
    $otpCode = $_POST['otp'] ?? '';
    
    if (strlen($otpCode) !== 6) {
        $error = "Please enter a valid 6-digit OTP.";
    } else {
        $result = verifyOTP($conn, $email, $purpose, $otpCode);
        
        if ($result['success']) {
            if ($purpose === 'signup_verification') {
                $safeEmail = mysqli_real_escape_string($conn, $email);
                mysqli_query($conn, "UPDATE users SET is_verified = 1, email_verified_at = NOW() WHERE email = '$safeEmail'");
                
                // Auto-login
                $resultUser = mysqli_query($conn, "SELECT * FROM users WHERE email = '$safeEmail' LIMIT 1");
                if ($resultUser && mysqli_num_rows($resultUser) > 0) {
                    $user = mysqli_fetch_assoc($resultUser);
                    session_regenerate_id(true);
                    $_SESSION['user'] = [
                        'id'    => $user['id'],
                        'name'  => $user['name'],
                        'email' => $user['email']
                    ];
                    
                    // Trigger signup success email
                    $shopUrl = "http" . (isset($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . "/index.php";
                    $data = [
                        'name' => $user['name'],
                        'shopUrl' => $shopUrl
                    ];
                    sendTransactionalEmail($user['email'], "Welcome to MATrends", 'signup_success', $data, 'user');
                    
                    $goto = $redirect ? $redirect : 'index.php';
                    session_write_close();
                    header("Location: " . $goto);
                    exit();
                } else {
                    $success = "Account verified successfully! You can now log in.";
                    echo "<script>setTimeout(() => { window.location.href = 'login.php" . ($redirect ? "?redirect=".urlencode($redirect) : "") . "'; }, 2000);</script>";
                }
            } elseif ($purpose === 'login_verification') {
                $safeEmail = mysqli_real_escape_string($conn, $email);
                mysqli_query($conn, "UPDATE users SET is_verified = 1 WHERE email = '$safeEmail' AND is_verified = 0");
                
                session_regenerate_id(true);
                $tempUser = $_SESSION['temp_user'];
                
                // Trigger login success email
                $resetUrl = "http" . (isset($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . "/forgot_password.php";
                $data = [
                    'name' => $tempUser['name'],
                    'resetUrl' => $resetUrl
                ];
                sendTransactionalEmail($tempUser['email'], "Login Successful on Your MATrends Account", 'login_success', $data, $tempUser['status']);
                
                if ($tempUser['status'] == 'admin') {
                    $_SESSION['admin'] = [
                        'id'    => $tempUser['id'],
                        'name'  => $tempUser['name'],
                        'email' => $tempUser['email']
                    ];
                    unset($_SESSION['temp_user']);
                    session_write_close();
                    header("Location: admins/dashboard.php");
                    exit();
                } else {
                    $_SESSION['user'] = [
                        'id'    => $tempUser['id'],
                        'name'  => $tempUser['name'],
                        'email' => $tempUser['email']
                    ];
                    unset($_SESSION['temp_user']);
                    $goto = $redirect ? $redirect : 'index.php';
                    session_write_close();
                    header("Location: " . $goto);
                    exit();
                }
            } elseif ($purpose === 'forgot_password') {
                $_SESSION['forgot_password_verified'] = $email;
                header("Location: reset_password.php");
                exit();
            }
        } else {
            $error = $result['message'];
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
                <?php if ($success): ?>
                    <div class="alert alert-success text-center">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <div class="ma-card p-4 p-md-5 ma-shadow">
                    <div class="text-center mb-4">
                        <img src="./assets/img/ma_trends_ill.png" alt="" width="80" loading="lazy" class="ma-pill">
                        <h1 class="h4 fw-bold mt-3 text-center">Verify Email</h1>
                        <p class="ma-muted small">We've sent a 6-digit code to <strong><?php echo htmlspecialchars($email); ?></strong>.</p>
                    </div>

                    <form method="POST" action="">
                        <input type="hidden" name="action" value="verify" />
                        
                        <div class="mb-4">
                            <label class="form-label ma-muted w-100 text-start">Enter OTP Code</label>
                            <div class="d-flex justify-content-between gap-2" id="otp-inputs">
                                <input class="form-control text-center fs-4 fw-bold rounded-3" style="width: 3.5rem; height: 3.5rem;" type="text" maxlength="1" pattern="\d" required autocomplete="off" autofocus />
                                <input class="form-control text-center fs-4 fw-bold rounded-3" style="width: 3.5rem; height: 3.5rem;" type="text" maxlength="1" pattern="\d" required autocomplete="off" />
                                <input class="form-control text-center fs-4 fw-bold rounded-3" style="width: 3.5rem; height: 3.5rem;" type="text" maxlength="1" pattern="\d" required autocomplete="off" />
                                <input class="form-control text-center fs-4 fw-bold rounded-3" style="width: 3.5rem; height: 3.5rem;" type="text" maxlength="1" pattern="\d" required autocomplete="off" />
                                <input class="form-control text-center fs-4 fw-bold rounded-3" style="width: 3.5rem; height: 3.5rem;" type="text" maxlength="1" pattern="\d" required autocomplete="off" />
                                <input class="form-control text-center fs-4 fw-bold rounded-3" style="width: 3.5rem; height: 3.5rem;" type="text" maxlength="1" pattern="\d" required autocomplete="off" />
                            </div>
                            <input type="hidden" name="otp" id="hidden-otp" value="" />
                        </div>

                        <button type="submit" class="btn btn-ma w-100">Verify OTP</button>
                    </form>

                    <div class="text-center mt-4">
                        <button id="resendBtn" class="btn btn-link text-decoration-none text-ma small" onclick="resendOTP()">Resend OTP</button>
                        <div id="resendMsg" class="small mt-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once('inc/bottom.php'); ?>

    <script>
        function resendOTP() {
            const btn = document.getElementById('resendBtn');
            const msg = document.getElementById('resendMsg');
            
            btn.disabled = true;
            btn.innerText = 'Sending...';
            
            const formData = new FormData();
            formData.append('email', '<?php echo addslashes($email); ?>');
            formData.append('purpose', '<?php echo addslashes($purpose); ?>');
            
            fetch('resend_otp.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    msg.innerHTML = '<span class="text-success">' + data.message + '</span>';
                    let timeLeft = 60;
                    btn.innerText = `Wait ${timeLeft}s`;
                    const timer = setInterval(() => {
                        timeLeft--;
                        if (timeLeft <= 0) {
                            clearInterval(timer);
                            btn.disabled = false;
                            btn.innerText = 'Resend OTP';
                            msg.innerHTML = '';
                        } else {
                            btn.innerText = `Wait ${timeLeft}s`;
                        }
                    }, 1000);
                } else {
                    msg.innerHTML = '<span class="text-danger">' + data.message + '</span>';
                    btn.disabled = false;
                    btn.innerText = 'Resend OTP';
                }
            })
            .catch(err => {
                msg.innerHTML = '<span class="text-danger">Network error. Try again.</span>';
                btn.disabled = false;
                btn.innerText = 'Resend OTP';
            });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('#otp-inputs input');
            const hiddenOtp = document.getElementById('hidden-otp');
            const form = document.querySelector('form');

            inputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    const val = e.target.value;
                    if (val.length > 1) {
                        e.target.value = val.slice(0, 1);
                    }
                    if (val !== '' && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                    updateHiddenOtp();
                });

                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                        inputs[index - 1].focus();
                    }
                });

                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
                    if (pastedData) {
                        for (let i = 0; i < pastedData.length; i++) {
                            if (inputs[i]) {
                                inputs[i].value = pastedData[i];
                            }
                        }
                        const focusIndex = Math.min(pastedData.length, 5);
                        inputs[focusIndex].focus();
                        updateHiddenOtp();
                    }
                });
            });

            function updateHiddenOtp() {
                let otpStr = '';
                inputs.forEach(input => {
                    otpStr += input.value;
                });
                hiddenOtp.value = otpStr;
            }

            form.addEventListener('submit', (e) => {
                updateHiddenOtp();
                if (hiddenOtp.value.length !== 6) {
                    e.preventDefault();
                    alert('Please enter all 6 digits of the OTP.');
                }
            });
        });
    </script>
</body>
</html>
