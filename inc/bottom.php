 <!-- jQuery + Bootstrap -->
  <!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->

  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
  <script src="assets/js/jquery.js"></script>
  <script src="assets/js/all.min.js"></script>
 
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>

  <!-- Notifications Toast Container (Top Right) -->
  <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100; margin-top: 70px;">
    <!-- Success Toast -->
    <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <i class="fas fa-check-circle me-2"></i> <span id="successToastMessage">Success message here</span>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
    <!-- Error Toast -->
    <div id="errorToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <i class="fas fa-circle-exclamation me-2"></i> <span id="errorToastMessage">Error message here</span>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>

  <!-- Legacy Cart Toast (Bottom Right) -->
  <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
    <div id="cartToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <i class="fas fa-check-circle me-2"></i> <span id="toastMessage">Product added to cart!</span>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>

  <!-- Logout Confirmation Modal -->
  <div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content ma-card border-0 ma-shadow">
              <div class="modal-body p-4 p-md-5 text-center">
                  <div class="mb-4">
                      <i class="fas fa-sign-out-alt fa-3x color-gold"></i>
                  </div>
                  <h3 class="h4 fw-bold mb-3 text-white">Confirm Logout</h3>
                  <p class="ma-muted mb-4">Are you sure you want to log out of your account?</p>
                  <div class="d-flex gap-2">
                      <button type="button" class="btn btn-ma-outline flex-grow-1" data-bs-dismiss="modal">Stay Logged In</button>
                      <a href="logout" class="btn btn-ma flex-grow-1">Logout</a>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <!-- WhatsApp Floating Button -->
  <a href="https://wa.me/923097410140" class="whatsapp-float" target="_blank">
      <i class="fab fa-whatsapp"></i>
  </a>