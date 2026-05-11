<?php
require_once('./assets/inc/admin_top.php');

if (!isset($_GET['id'])) {
    header("Location: orders");
    exit;
}

$id = (int)$_GET['id'];
$sqlOrder = "SELECT * FROM orders WHERE id = $id";
$resOrder = mysqli_query($conn, $sqlOrder);

if (!$resOrder || mysqli_num_rows($resOrder) == 0) {
    header("Location: orders");
    exit;
}
$order = mysqli_fetch_assoc($resOrder);

// Fetch order details
$sqlDetails = "SELECT * FROM order_details WHERE order_id = $id";
$resDetails = mysqli_query($conn, $sqlDetails);

// Determine status text/color
$statusText = 'Pending';
$statusBadge = 'bg-warning text-dark';
if ($order['status'] == 1) {
    $statusText = 'Delivered';
    $statusBadge = 'bg-success';
} elseif ($order['status'] == 2) {
    $statusText = 'Cancelled';
    $statusBadge = 'bg-danger';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #ORD-<?php echo $order['id']; ?></title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- html2pdf library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        body {
            background: #f5f5f5;
            font-family: Arial, sans-serif;
        }

        .invoice-box {
            max-width: 1000px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .store-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .invoice-title {
            color: #333;
            font-weight: bold;
        }

        .status-badge {
            font-size: 14px;
        }

        .table tfoot {
            background: #f8f9fa;
            font-weight: bold;
        }

        .section-title {
            font-weight: 600;
            margin-bottom: 12px;
            color: #444;
        }
        
        .print-actions {
            max-width: 1000px;
            margin: 0 auto 40px;
            text-align: right;
        }
    </style>
</head>

<body>

    <div class="container">
        
        <!-- The section we want to print as PDF -->
        <div class="invoice-box" id="invoiceContent">

            <!-- Header -->
            <div class="row align-items-center mb-4">
                <div class="col-md-6 d-flex align-items-center gap-3">
                    <img src="../assets/img/ma_trends_ill.png" class="store-logo">

                    <div>
                        <h3 class="mb-0">MATrends Store</h3>
                        <small>Premium Shopping Experience</small>
                    </div>
                </div>

                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <h2 class="invoice-title">INVOICE</h2>
                    <p class="mb-1">
                        <strong>Order ID:</strong> #ORD-<?php echo $order['id']; ?>
                    </p>
                    <p class="mb-1 small text-muted">
                        <strong>Date:</strong> <?php echo date('M d, Y - h:i A', strtotime($order['created_at'])); ?>
                    </p>

                    <span class="badge <?php echo $statusBadge; ?> status-badge mt-2">
                        <?php echo $statusText; ?>
                    </span>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="row mb-4">

                <div class="col-md-6">
                    <h5 class="section-title">Customer Details</h5>

                    <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
                    <p class="mb-1"><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                    <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                </div>

                <div class="col-md-6">
                    <h5 class="section-title">Shipping Details</h5>
                    <p class="mb-1"><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                    <p class="mb-1"><strong>City:</strong> <?php echo htmlspecialchars($order['city']); ?></p>
                    <p class="mb-1"><strong>Region:</strong> <?php echo htmlspecialchars($order['region']); ?></p>
                    <p class="mb-1"><strong>Postal Code:</strong> <?php echo htmlspecialchars($order['postalcode']); ?></p>
                    <p class="mb-1 mt-2"><strong>Payment:</strong> <span class="text-uppercase"><?php echo htmlspecialchars($order['payment_method']); ?></span></p>
                </div>

            </div>

            <!-- Products Table -->
            <div class="table-responsive">
                <table class="table table-bordered text-center" id="invoiceTable">

                    <thead class="table-dark">
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>

                    <tbody id="productBody">
                        <?php 
                        if ($resDetails && mysqli_num_rows($resDetails) > 0) {
                            while ($item = mysqli_fetch_assoc($resDetails)) {
                        ?>
                            <tr>
                                <td class="text-start ps-3"><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo $item['qty']; ?></td>
                                <td class="text-end pe-3">Rs. <?php echo number_format($item['sub_total'], 2); ?></td>
                            </tr>
                        <?php 
                            }
                        }
                        ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-end">
                                Total Amount
                            </td>
                            <td id="grandTotal" class="text-end pe-3">Rs. <?php echo number_format($order['total'], 2); ?></td>
                        </tr>
                    </tfoot>

                </table>
            </div>

        </div>
        
        <!-- Print / Actions -->
        <div class="print-actions">
            <a href="orders" class="btn btn-secondary me-2">Back to Orders</a>
            <button id="downloadPdfBtn" class="btn btn-primary">Download PDF</button>
        </div>
        
    </div>

    <script>
        document.getElementById('downloadPdfBtn').addEventListener('click', function() {
            // Target the invoice box container
            const element = document.getElementById('invoiceContent');
            
            // Generate the filename using the Order ID
            const filename = 'Invoice_ORD_<?php echo $order['id']; ?>.pdf';
            
            // html2pdf options
            const opt = {
                margin:       [0.5, 0.5, 0.5, 0.5],
                filename:     filename,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, scrollY: 0 },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' },
                pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
            };
            
            // Temporary change styles for better PDF output
            const originalShadow = element.style.boxShadow;
            element.style.boxShadow = 'none'; // remove shadow in pdf
            
            // Remove table-responsive temporarily to prevent horizontal clipping
            const tableResp = element.querySelector('.table-responsive');
            if (tableResp) {
                tableResp.classList.remove('table-responsive');
            }
            
            // Execute generation
            html2pdf().set(opt).from(element).save().then(() => {
                // Restore styles after generation
                element.style.boxShadow = originalShadow;
                if (tableResp) {
                    tableResp.classList.add('table-responsive');
                }
            });
        });
    </script>

</body>

</html>