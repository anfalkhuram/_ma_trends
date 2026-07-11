<?php

$pageTitle = "Secure Checkout | MATrends";

$description = "Complete your order securely at MATrends. Review your products, shipping details and payment information before placing your order.";

$keywords = "MATrends checkout, secure checkout, online payment, place order";

$author = "MATrends";
$robots = "noindex, nofollow";

$ogTitle = "Secure Checkout | MATrends";
$ogDescription = "Complete your purchase securely at MATrends.";
$ogType = "website";
$ogUrl = "https://www.matrends.store/checkout";
require_once('inc/top.php')
?>

<body>
    <?php
    require_once('inc/navbar.php');
    
    // Ensure the user is logged in and redirect back here after login if not
    requireLogin('checkout');

    $checkoutUserId = $_SESSION['user']['id'] ?? $_SESSION['admin']['id'] ?? 0;
    $checkoutUserData = [];
    if ($checkoutUserId > 0) {
        $sqlUser = "SELECT * FROM users WHERE id = $checkoutUserId";
        $resUser = mysqli_query($conn, $sqlUser);
        if ($resUser) {
            $checkoutUserData = mysqli_fetch_assoc($resUser);
            
            // If user is not verified, force OTP verification
            if ($checkoutUserData['is_verified'] != 1) {
                require_once('inc/otp_functions.php');
                $role = $checkoutUserData['status'] == 'admin' ? 'admin' : 'user';
                storeAndSendOTP($conn, $checkoutUserData['email'], $role, 'login_verification');
                
                // Set temp_user to continue the flow
                $_SESSION['temp_user'] = [
                    'id'    => $checkoutUserData['id'],
                    'name'  => $checkoutUserData['name'],
                    'email' => $checkoutUserData['email'],
                    'status'=> $checkoutUserData['status']
                ];
                
                // Unset active session to lock them out until verified
                if(isset($_SESSION['user'])) unset($_SESSION['user']);
                if(isset($_SESSION['admin'])) unset($_SESSION['admin']);
                
                session_write_close();
                echo "<script>window.location.href='verify_otp.php?purpose=login_verification&redirect=checkout';</script>";
                exit();
            }
        }
    }
    ?>

    <main class="ma-hero pb-5">
        <div class="container">
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
                <div>
                    <div class="ma-kicker mb-2">Checkout</div>
                    <h1 class="h2 fw-bold mb-1">Shipping & Payment</h1>

                </div>
                <a class="btn btn-ma-outline" href="cart">Back to cart</a>
            </div>

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="ma-card p-3 p-md-4 mb-3">
                        <h2 class="h5 fw-bold mb-3">Shipping details</h2>
                        <div class="row g-3" id="shippingDetailsForm">
                            <div class="col-md-6">
                                <label class="form-label ma-muted">Full name</label>
                                <input class="form-control" name="shipping_name" placeholder="Alex Doe" value="<?php echo htmlspecialchars($checkoutUserData['name'] ?? ''); ?>" readonly />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label ma-muted">Email</label>
                                <input class="form-control" name="shipping_email" placeholder="you@example.com" value="<?php echo htmlspecialchars($checkoutUserData['email'] ?? ''); ?>" readonly />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label ma-muted">Phone</label>
                                <input class="form-control" name="shipping_phone" placeholder="+1 555 123 4567" value="<?php echo htmlspecialchars($checkoutUserData['phone'] ?? ''); ?>" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label ma-muted">City</label>
                                <input class="form-control" name="shipping_city" placeholder="City" value="<?php echo htmlspecialchars($checkoutUserData['city'] ?? ''); ?>" />
                            </div>
                            <div class="col-12">
                                <label class="form-label ma-muted">Address</label>
                                <input class="form-control" name="shipping_address" placeholder="Street, building, etc." value="<?php echo htmlspecialchars($checkoutUserData['address'] ?? ''); ?>" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label ma-muted">State / Region</label>
                                <input class="form-control" name="shipping_region" placeholder="State" value="<?php echo htmlspecialchars($checkoutUserData['state'] ?? ''); ?>" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label ma-muted">Postal code</label>
                                <input class="form-control" name="shipping_postalcode" placeholder="ZIP" value="<?php echo htmlspecialchars($checkoutUserData['postal_code'] ?? ''); ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="ma-card p-3 p-md-4">
                        <h2 class="h5 fw-bold mb-3">Payment method</h2>
                        <div class="d-flex flex-column gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentMethod" value="cod" id="pay1" checked />
                                <label class="form-check-label ma-muted" for="pay1">Cash on Delivery</label>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input mt-4" type="radio" name="paymentMethod" value="easypaisa" id="pay2" />
                                <label class="form-check-label ma-muted" for="pay2">
                                    <img src="./assets/img/Easypaisa.png" alt="easypaisa" width="120" class="bg-light p-3 rounded"/>
                                </label>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input mt-4" type="radio" name="paymentMethod" value="jazzcash" id="pay3" />
                                <label class="form-check-label ma-muted" for="pay3">
                                    <img src="./assets/img/JazzCash.png" alt="jazzCash" width="120" class="bg-light p-3 rounded"/>
                                </label>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input mt-2" type="radio" name="paymentMethod" value="bank_transfer" id="pay4" />
                                <label class="form-check-label ma-muted fw-bold d-flex align-items-center" for="pay4" style="height: 38px;">
                                    <i class="fas fa-university me-2 fs-4"></i> Bank Transfer
                                </label>
                            </div>
                        </div>
                       
                        
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="ma-card p-3 p-md-4">
                        <h2 class="h5 fw-bold mb-3">Order summary</h2>
                        <?php
                        $userId = $_SESSION['user']['id'] ?? $_SESSION['admin']['id'] ?? 0;
                        $subtotal = 0;
                        if ($userId > 0) {
                            $sqlCart = "SELECT c.*, p.name as product_name 
                                        FROM cart c 
                                        JOIN products p ON c.product_id = p.id 
                                        WHERE c.user_id = $userId";
                            $resCart = mysqli_query($conn, $sqlCart);
                            while ($item = mysqli_fetch_assoc($resCart)) {
                                $subtotal += $item['sub_total'];
                                ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <div class="ma-muted"><?php echo htmlspecialchars($item['product_name']); ?> (x<?php echo $item['quantity']; ?>)</div>
                                    <div class="ma-muted">Rs. <?php echo number_format($item['sub_total'], 2); ?></div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                        <hr class="border ma-border my-3" />
                        <div class="d-flex justify-content-between mb-2">
                            <div class="ma-muted">Subtotal</div>
                            <div class="js-cart-subtotal">Rs. <?php echo number_format($subtotal, 2); ?></div>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <div class="ma-muted">Delivery </div>
                            <div class="js-cart-shipping" data-shipping="0">Free</div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="fw-bold">Total</div>
                            <div class="h5 ma-price js-cart-total">Rs. <?php echo number_format($subtotal, 2); ?></div>
                        </div>
                        <button class="btn btn-ma w-100 mt-3" type="button" id="placeOrderBtn">Place order</button>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white" id="paymentModalTitle">Payment Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body ma-muted">
                    <p>Please send the payment to the following account:</p>
                    <div class="bg-dark p-3 rounded mb-3 border ma-border">
                        <p class="mb-1 text-white">Account Title: <strong id="paymentAccountTitle">MATrends Store</strong></p>
                        <p class="mb-1 text-white">Account Number: <strong id="paymentAccountNumber">03001234567</strong></p>
                        <p class="mb-1 text-white" id="paymentBankWrapper" style="display: none;">Bank: <strong id="paymentBankName"></strong></p>
                    </div>
                    <p>After sending the payment, please upload a screenshot of the transaction receipt.</p>
                    <div class="mb-3">
                        <label for="paymentScreenshot" class="form-label text-white">Upload Screenshot</label>
                        <input class="form-control bg-dark text-white border-secondary" type="file" id="paymentScreenshot" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-ma-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-ma" id="confirmPaymentBtn">Confirm & Place Order</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="placeOrderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ma-bg-surface border ma-border ma-rounded">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white">Order placed successfully.</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body ma-muted">
                    After payment confirmation, your order will be processed and prepared for dispatch.
                </div>
                <div class="modal-footer border-0">
                    <a class="btn btn-ma" href="index">Back to home</a>
                </div>
            </div>
        </div>
    </div>

    <?php
    require_once('inc/footer.php');
    ?>

    <?php
    require_once('inc/bottom.php');
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const placeOrderBtn = document.getElementById('placeOrderBtn');
            const confirmPaymentBtn = document.getElementById('confirmPaymentBtn');
            const paymentDetailsModalEl = document.getElementById('paymentDetailsModal');
            const placeOrderModalEl = document.getElementById('placeOrderModal');
            
            // Bootstrap modals
            const paymentDetailsModal = new bootstrap.Modal(paymentDetailsModalEl);
            const placeOrderModal = new bootstrap.Modal(placeOrderModalEl);
            
            // Clear validation error on input
            const shippingInputs = document.querySelectorAll('#shippingDetailsForm input');
            shippingInputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value.trim() !== '') {
                        this.classList.remove('is-invalid');
                    }
                });
            });

            placeOrderBtn.addEventListener('click', function() {
                // Validate Shipping Form
                let isFormValid = true;
                shippingInputs.forEach(input => {
                    if (input.value.trim() === '') {
                        isFormValid = false;
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });

                if (!isFormValid) {
                    const errorMsg = document.getElementById('errorToastMessage');
                    if (errorMsg) errorMsg.textContent = 'Please complete the form to proceed further.';
                    const errorToast = bootstrap.Toast.getOrCreateInstance(document.getElementById('errorToast'));
                    errorToast.show();
                    return;
                }

                const selectedPayment = document.querySelector('input[name="paymentMethod"]:checked');
                
                if (!selectedPayment) {
                    const errorMsg = document.getElementById('errorToastMessage');
                    if (errorMsg) errorMsg.textContent = 'Please select a payment method.';
                    const errorToast = bootstrap.Toast.getOrCreateInstance(document.getElementById('errorToast'));
                    errorToast.show();
                    return;
                }

                if (selectedPayment.value === 'easypaisa' || selectedPayment.value === 'jazzcash' || selectedPayment.value === 'bank_transfer') {
                    const accNum = document.getElementById('paymentAccountNumber');
                    const accTitle = document.getElementById('paymentAccountTitle');
                    const bankWrap = document.getElementById('paymentBankWrapper');
                    const bankName = document.getElementById('paymentBankName');
                    const title = document.getElementById('paymentModalTitle');
                    
                    if (selectedPayment.value === 'easypaisa') {
                        title.textContent = 'Easypaisa Payment Details';
                        accTitle.textContent = 'Muhammed Ahmed';
                        accNum.textContent = '03171417715';
                        bankWrap.style.display = 'none';
                    } else if (selectedPayment.value === 'jazzcash') {
                        title.textContent = 'JazzCash Payment Details';
                        accTitle.textContent = 'Kaneez Bibi';
                        accNum.textContent = '03097886931';
                        bankWrap.style.display = 'none';
                    } else if (selectedPayment.value === 'bank_transfer') {
                        title.textContent = 'Bank Transfer Details';
                        accTitle.textContent = 'Muhammad Ahmad Asan Account';
                        accNum.textContent = '1791006506620001';
                        bankName.textContent = 'MCB Islamic';
                        bankWrap.style.display = 'block';
                    }
                    paymentDetailsModal.show();
                } else if (selectedPayment.value === 'cod') {
                    submitOrder(selectedPayment.value);
                }
            });

            confirmPaymentBtn.addEventListener('click', function() {
                const screenshotInput = document.getElementById('paymentScreenshot');
                if (!screenshotInput.files || screenshotInput.files.length === 0) {
                    const errorMsg = document.getElementById('errorToastMessage');
                    if (errorMsg) errorMsg.textContent = 'Please upload a screenshot of your transaction receipt.';
                    const errorToast = bootstrap.Toast.getOrCreateInstance(document.getElementById('errorToast'));
                    errorToast.show();
                    return;
                }
                
                const selectedPayment = document.querySelector('input[name="paymentMethod"]:checked').value;
                submitOrder(selectedPayment, screenshotInput.files[0]);
            });

            function submitOrder(paymentMethod, screenshotFile = null) {
                // Change button state
                const originalTextBtn = confirmPaymentBtn.innerHTML;
                const originalTextPlace = placeOrderBtn.innerHTML;
                
                confirmPaymentBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                confirmPaymentBtn.disabled = true;
                placeOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                placeOrderBtn.disabled = true;

                const formData = new FormData();
                formData.append('paymentMethod', paymentMethod);
                
                // Append shipping fields
                shippingInputs.forEach(input => {
                    formData.append(input.name, input.value.trim());
                });

                if (screenshotFile) {
                    formData.append('paymentScreenshot', screenshotFile);
                }

                fetch('./process_checkout.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Success flow
                        if (paymentMethod !== 'cod') {
                            paymentDetailsModal.hide();
                            paymentDetailsModalEl.addEventListener('hidden.bs.modal', function onHidden() {
                                placeOrderModal.show();
                                paymentDetailsModalEl.removeEventListener('hidden.bs.modal', onHidden);
                            });
                        } else {
                            placeOrderModal.show();
                        }

                        // Update cart count badge if needed (optional)
                        const cartCountEl = document.querySelector('.js-cart-count');
                        if (cartCountEl) cartCountEl.textContent = '0';
                        
                    } else {
                        // Server validation error
                        const errorMsg = document.getElementById('errorToastMessage');
                        if (errorMsg) errorMsg.textContent = data.message || 'Error processing order.';
                        const errorToast = bootstrap.Toast.getOrCreateInstance(document.getElementById('errorToast'));
                        errorToast.show();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const errorMsg = document.getElementById('errorToastMessage');
                    if (errorMsg) errorMsg.textContent = 'A network error occurred. Please try again.';
                    const errorToast = bootstrap.Toast.getOrCreateInstance(document.getElementById('errorToast'));
                    errorToast.show();
                })
                .finally(() => {
                    confirmPaymentBtn.innerHTML = originalTextBtn;
                    confirmPaymentBtn.disabled = false;
                    placeOrderBtn.innerHTML = originalTextPlace;
                    placeOrderBtn.disabled = false;
                });
            }
        });
    </script>
</body>

</html>