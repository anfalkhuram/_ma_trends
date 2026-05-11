/* MA Trends — Global jQuery interactions */
(function ($) {
  "use strict";

  function money(n) {
    try { return new Intl.NumberFormat(undefined, { style: "currency", currency: "USD" }).format(n); }
    catch (e) { return "$" + Number(n || 0).toFixed(2); }
  }

  // Wishlist toggle
  $(document).on("click", ".js-wishlist", function (e) {
    e.preventDefault();
    var $btn = $(this);
    var liked = $btn.attr("data-liked") === "true";
    $btn.attr("data-liked", liked ? "false" : "true");
    $btn.find("[data-icon]").text(liked ? "♡" : "♥");
  });

  // Product card "quick view" modal
  $(document).on("click", ".js-quickview", function (e) {
    e.preventDefault();
    var $t = $(this);
    var title = $t.data("title") || "Product";
    var price = $t.data("price") || "";
    var img = $t.data("img") || "";
    $("#quickViewTitle").text(title);
    $("#quickViewPrice").text(price);
    if (img) $("#quickViewImg").attr("src", img);
    var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById("quickViewModal"));
    modal.show();
  });

  // Shop filters
  function applyFilters() {
    var cat = $("#filterCategory").val() || "all";
    var gender = $("#filterGender").val() || "all";
    var price = $("#filterPrice").val() || "all";

    $(".js-product").each(function () {
      var $p = $(this);
      var pCat = ($p.data("category") || "").toString();
      var pGender = ($p.data("gender") || "").toString();
      var pPrice = Number($p.data("price") || 0);

      var ok = true;
      if (cat !== "all" && pCat !== cat) ok = false;
      if (gender !== "all" && pGender !== gender) ok = false;
      if (price !== "all") {
        if (price === "0-50" && !(pPrice >= 0 && pPrice <= 50)) ok = false;
        if (price === "50-120" && !(pPrice > 50 && pPrice <= 120)) ok = false;
        if (price === "120-9999" && !(pPrice > 120)) ok = false;
      }
      $p.toggleClass("d-none", !ok);
    });
  }
  $(document).on("change", "#filterCategory, #filterGender, #filterPrice", applyFilters);
  $(document).on("click", ".js-clear-filters", function (e) {
    e.preventDefault();
    $("#filterCategory").val("all");
    $("#filterGender").val("all");
    $("#filterPrice").val("all");
    applyFilters();
  });

  // Quantity steppers (input.js-qty with buttons .js-qty-minus/.js-qty-plus)
  $(document).on("click", ".js-qty-minus, .js-qty-plus", function (e) {
    e.preventDefault();
    var $btn = $(this);
    var $wrap = $btn.closest(".js-qty-wrap");
    var $input = $wrap.find(".js-qty");
    var $cartItem = $btn.closest(".js-cart-item");
    
    var v = parseInt($input.val() || "1", 10);
    var action = $btn.hasClass("js-qty-plus") ? "plus" : "minus";

    if ($cartItem.length) {
      // Cart page: update database
      var cartId = $cartItem.data("id");
      $wrap.find("button").prop("disabled", true);

      $.ajax({
        url: "update_cart_qty.php",
        method: "POST",
        data: { cart_id: cartId, action: action },
        dataType: "json",
        success: function(response) {
          if (response.success) {
            loadCart();
          } else {
            alert(response.message);
          }
        },
        error: function() {
          alert("Error updating quantity.");
        },
        complete: function() {
          $wrap.find("button").prop("disabled", false);
        }
      });
    } else {
      // Product page: just UI
      if (action === "minus") v = Math.max(1, v - 1);
      else v = v + 1;
      $input.val(v).trigger("change");
    }
  });

  // Cart totals (UI only - fallback)
  function recalcCart() {
    // This is mostly for UI-only pages if any, but cart.php now uses AJAX
  }

  var itemToRemove = null;
  $(document).on("click", ".js-remove-cart", function (e) {
    e.preventDefault();
    itemToRemove = $(this);
    var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById("removeConfirmModal"));
    modal.show();
  });

  function loadCart() {
    $("#cartDataContainer").addClass("opacity-50");
    $.ajax({
      url: "load_cart_data.php",
      method: "GET",
      success: function(html) {
        $("#cartDataContainer").html(html).removeClass("opacity-50");
      },
      error: function() {
        $("#cartDataContainer").removeClass("opacity-50");
        alert("Error reloading cart data.");
      }
    });
  }

  $(document).on("click", "#confirmRemoveBtn", function () {
    if (!itemToRemove) return;
    
    var $btn = itemToRemove;
    var cartId = $btn.data("id");
    var $confirmBtn = $(this);

    $confirmBtn.prop("disabled", true).text("Removing...");

    $.ajax({
        url: "remove_from_cart.php",
        method: "POST",
        data: { cart_id: cartId },
        dataType: "json",
        success: function(response) {
            if (response.success) {
                var modal = bootstrap.Modal.getInstance(document.getElementById("removeConfirmModal"));
                modal.hide();
                loadCart();
                $(".js-cart-count").text(response.cart_count);
            } else {
                alert(response.message);
            }
        },
        complete: function() {
            $confirmBtn.prop("disabled", false).text("Remove");
            itemToRemove = null;
        }
    });
  });

  $(document).on("click", ".js-logout", function (e) {
    e.preventDefault();
    var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById("logoutConfirmModal"));
    modal.show();
  });

  // Add to Cart AJAX
  $(document).on("click", ".js-add-to-cart", function (e) {
    e.preventDefault();
    var $btn = $(this);
    var productId = $btn.data("product-id");
    var quantity = $(".js-qty").length ? $(".js-qty").val() : 1;

    $btn.prop("disabled", true).addClass("opacity-50");

    $.ajax({
      url: "add_to_cart.php",
      method: "POST",
      data: { product_id: productId, quantity: quantity },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          $("#toastMessage").text(response.message);
          var toast = bootstrap.Toast.getOrCreateInstance(document.getElementById("cartToast"));
          toast.show();
          if (response.cart_count !== undefined) {
            $(".js-cart-count").text(response.cart_count);
          }
        } else {
          if (response.message.indexOf("login") !== -1) {
            window.location.href = "login?redirect=" + encodeURIComponent(window.location.pathname + window.location.search);
          } else {
            alert(response.message);
          }
        }
      },
      complete: function () {
        $btn.prop("disabled", false).removeClass("opacity-50");
      }
    });
  });

  // Product gallery
  $(document).on("click", ".js-thumb", function (e) {
    e.preventDefault();
    var src = $(this).data("src");
    if (!src) return;
    $(".js-main-img").attr("src", src);
    $(".js-thumb").removeClass("active");
    $(this).addClass("active");
  });

  // Zoom
  $(document).on("click", ".js-zoom", function (e) {
    e.preventDefault();
    var src = $(".js-main-img").attr("src");
    if (src) $("#zoomImg").attr("src", src);
    var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById("zoomModal"));
    modal.show();
  });

  // Admin Table
  (function adminTable() {
    var $wrap = $(".ma-admin-table-wrap");
    if (!$wrap.length) return;
    // ... (rest of admin table logic)
  })();

  // Init
  $(function () {
    applyFilters();
    // recalcCart();
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });
  });
})(jQuery);

// === Product search dropdown ===
(function initProductSearch() {
  var searchInput  = document.getElementById('productSearchInput');
  var hiddenId     = document.getElementById('productIdInput');
  var hiddenSelect = document.getElementById('productSelectHidden');
  var dropdown     = document.getElementById('productDropdown');
  if (!searchInput || !hiddenSelect || !dropdown) return;

  var allProducts = Array.from(hiddenSelect.options)
    .filter(function (o) { return o.value !== ''; })
    .map(function (o) { return { id: o.value, name: o.text }; });

  function renderList(items) {
    dropdown.innerHTML = '';
    if (!items.length) {
      var li = document.createElement('li');
      li.textContent = 'No products found';
      li.classList.add('ma-pd-empty');
      dropdown.appendChild(li);
    } else {
      items.forEach(function (p) {
        var li = document.createElement('li');
        li.textContent = p.name;
        li.dataset.id  = p.id;
        li.addEventListener('mousedown', function (e) {
          e.preventDefault();
          searchInput.value = p.name;
          hiddenId.value    = p.id;
          dropdown.classList.remove('show');
        });
        dropdown.appendChild(li);
      });
    }
    dropdown.classList.add('show');
  }

  searchInput.addEventListener('input', function () {
    var q = searchInput.value.trim().toLowerCase();
    hiddenId.value = '';
    if (!q) { dropdown.classList.remove('show'); return; }
    renderList(allProducts.filter(function (p) { return p.name.toLowerCase().indexOf(q) !== -1; }));
  });

  searchInput.addEventListener('focus', function () {
    var q = searchInput.value.trim().toLowerCase();
    if (q) renderList(allProducts.filter(function (p) { return p.name.toLowerCase().indexOf(q) !== -1; }));
  });

  document.addEventListener('click', function (e) {
    if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.classList.remove('show');
    }
  });
})();