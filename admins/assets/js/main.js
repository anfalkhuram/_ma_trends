/* MA Trends — Global jQuery interactions (Front-end only) */
(function ($) {
  "use strict";

  function money(n) {
    try { return new Intl.NumberFormat(undefined, { style: "currency", currency: "USD" }).format(n); }
    catch (e) { return "$" + Number(n || 0).toFixed(2); }
  }

  // Wishlist toggle (button with .js-wishlist)
  $(document).on("click", ".js-wishlist", function (e) {
    e.preventDefault();
    var $btn = $(this);
    var liked = $btn.attr("data-liked") === "true";
    $btn.attr("data-liked", liked ? "false" : "true");
    $btn.find("[data-icon]").text(liked ? "♡" : "♥");
  });

  // Product card "quick view" modal (optional)
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

  // Shop filters (simple front-end filtering by data attributes)
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
    var v = parseInt($input.val() || "1", 10);
    if ($btn.hasClass("js-qty-minus")) v = Math.max(1, v - 1);
    if ($btn.hasClass("js-qty-plus")) v = v + 1;
    $input.val(v).trigger("change");
  });

  // Cart totals (UI only)
  function recalcCart() {
    var subtotal = 0;
    $(".js-cart-item").each(function () {
      var $row = $(this);
      var price = Number($row.data("price") || 0);
      var qty = parseInt($row.find(".js-qty").val() || "1", 10);
      var line = price * qty;
      subtotal += line;
      $row.find(".js-line-total").text(money(line));
    });
    $(".js-cart-subtotal").text(money(subtotal));
    var shipping = Number($(".js-cart-shipping").data("shipping") || 0);
    $(".js-cart-total").text(money(subtotal + shipping));
  }
  $(document).on("change", ".js-cart-item .js-qty", recalcCart);
  $(document).on("click", ".js-remove-cart", function (e) {
    e.preventDefault();
    $(this).closest(".js-cart-item").remove();
    recalcCart();
  });

  // Product gallery (thumbs swap main image)
  $(document).on("click", ".js-thumb", function (e) {
    e.preventDefault();
    var src = $(this).data("src");
    if (!src) return;
    $(".js-main-img").attr("src", src);
    $(".js-thumb").removeClass("active");
    $(this).addClass("active");
  });

  // "Zoom" = open main image in modal
  $(document).on("click", ".js-zoom", function (e) {
    e.preventDefault();
    var src = $(".js-main-img").attr("src");
    if (src) $("#zoomImg").attr("src", src);
    var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById("zoomModal"));
    modal.show();
  });

  // Admin sidebar toggle (collapse to icons / slide on mobile)
  $(document).on("click", ".js-toggle-sidebar", function (e) {
    e.preventDefault();
    $(".ma-admin-shell").toggleClass("ma-admin-collapsed");
    $(this).toggleClass("no-margin-left");
  });

  // --- Admin table: client-side search, entries-per-page, pagination ---
  (function adminTable() {
    var $wrap = $(".ma-admin-table-wrap");
    if (!$wrap.length) return;

    var $tbody = $wrap.find(".js-admin-tbody");
    var $rows = $tbody.find(".js-admin-row");
    var $entriesSelect = $wrap.find(".js-admin-entries");
    var $searchInput = $wrap.find(".js-admin-search");
    var $footerInfo = $wrap.find(".js-admin-footer-info");
    var $pagination = $wrap.find(".js-admin-pagination");


    var currentPage = 1;
    var entriesPerPage = parseInt($entriesSelect.val() || "5", 10);
    var searchTerm = "";

    function getFilteredRows() {
      if (!searchTerm) return $rows;
      var term = searchTerm.toLowerCase();
      return $rows.filter(function () {
        var $r = $(this);
        var name = ($r.data("name") || "").toString().toLowerCase();
        var slug = ($r.data("slug") || "").toString().toLowerCase();
        var id = ($r.data("id") || "").toString();
        return name.indexOf(term) !== -1 || slug.indexOf(term) !== -1 || id.indexOf(term) !== -1;
      });
    }

    var sortDir = null;

    function sortById($rows) {
      if (!sortDir) return $rows;

      var sorted = $rows.get().sort(function (a, b) {
        var aId = Number($(a).data("id"));
        var bId = Number($(b).data("id"));

        return sortDir === "asc" ? aId - bId : bId - aId;
      });

      return $(sorted);
    }


    function render() {
      var $filtered = sortById(getFilteredRows());
      // $tbody.append($filtered);
      $tbody.empty().append($filtered); // clear tbody before appending
      var total = $filtered.length;

      if (total === 0) {
        // Count the number of columns from the first visible row
        var colCount = $wrap.find('tbody tr:first td').length;

        // Fallback: if tbody is empty, count the <th> in thead
        if (!colCount) {
          colCount = $wrap.find('thead th').length || 1;
        }
        // If no rows match, show a single "No records found" row
        $tbody.html('<tr class="no-record"><td colspan="' + colCount + '" class="text-center">No records found</td></tr>');
        $footerInfo.text("Showing 0 to 0 of 0 entries");
        $pagination.html(''); // clear pagination
        return; // exit early
      }
      var total = $filtered.length;
      var start = (currentPage - 1) * entriesPerPage;
      var end = Math.min(start + entriesPerPage, total);

      $rows.addClass("d-none");
      $filtered.slice(start, end).removeClass("d-none");

      $footerInfo.text("Showing " + (total ? start + 1 : 0) + " to " + end + " of " + total + " entries");

      var totalPages = Math.max(1, Math.ceil(total / entriesPerPage));
      currentPage = Math.min(Math.max(1, currentPage), totalPages);

      var html = "";
      if (currentPage > 1) {
        html += '<li class="page-item"><a class="page-link js-admin-page" href="#" data-page="' + (currentPage - 1) + '">Previous</a></li>';
      }
      for (var p = 1; p <= totalPages; p++) {
        html += '<li class="page-item' + (p === currentPage ? ' active' : '') + '"><a class="page-link js-admin-page" href="#" data-page="' + p + '">' + p + '</a></li>';
      }
      if (currentPage < totalPages) {
        html += '<li class="page-item"><a class="page-link js-admin-page" href="#" data-page="' + (currentPage + 1) + '">Next</a></li>';
      }
      $pagination.html(html);
    }

    $entriesSelect.on("change", function () {
      entriesPerPage = parseInt($(this).val(), 10);
      currentPage = 1;
      render();
    });

    $searchInput.on("input", function () {
      searchTerm = $(this).val().trim();
      currentPage = 1;
      render();
    });

    $(document).on("click", ".js-admin-page", function (e) {
      e.preventDefault();
      var p = parseInt($(this).data("page"), 10);
      if (!isNaN(p)) {
        currentPage = p;
        render();
      }
    });



    $(document).on("click", ".ma-sort-arrow", function () {
      var newDir = $(this).data("dir");

      // Force re-sort even if same direction
      if (sortDir === newDir) {
        sortDir = newDir === "asc" ? "desc" : "asc";
      } else {
        sortDir = newDir;
      }

      $(".ma-sort-arrow").removeClass("active");
      $('.ma-sort-arrow[data-dir="' + sortDir + '"]').addClass("active");

      currentPage = 1;
      render();
    });




    render();
  })();

  // Init (on load)
  $(function () {
    applyFilters();
    recalcCart();
  });
})(jQuery);

