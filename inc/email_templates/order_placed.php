<h2>Thank you for your order!</h2>
<p>Hello <?php echo htmlspecialchars($name); ?>,</p>
<p>Your order <strong>#ORD-<?php echo htmlspecialchars($orderId); ?></strong> has been successfully placed.</p>

<table class="data-table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($items as $item): ?>
        <tr>
            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
            <td><?php echo (int)$item['qty']; ?></td>
            <td>Rs. <?php echo number_format($item['sub_total'], 2); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<table class="data-table" style="width: 100%; border:none; margin-top: 0;">
    <tr style="border:none;">
        <td style="border:none; text-align:right;"><strong>Shipping:</strong></td>
        <td style="border:none; text-align:right; width: 120px;">Rs. <?php echo number_format($shippingCost, 2); ?></td>
    </tr>
    <tr style="border:none;">
        <td style="border:none; text-align:right; font-size:18px;"><strong>Total:</strong></td>
        <td style="border:none; text-align:right; font-size:18px; color:#111; font-weight:bold;">Rs. <?php echo number_format($total, 2); ?></td>
    </tr>
</table>

<div style="background-color: #f8f9fa; padding: 15px; border-radius: 6px; margin-top: 20px;">
    <h3 style="margin-top:0; font-size:16px;">Order Details</h3>
    <p style="margin-bottom: 5px;"><strong>Payment Method:</strong> <span style="text-transform:uppercase;"><?php echo htmlspecialchars($paymentMethod); ?></span></p>
    <p style="margin-bottom: 5px;"><strong>Shipping Address:</strong><br>
        <?php echo htmlspecialchars($address); ?><br>
        <?php echo htmlspecialchars($city . ', ' . $region . ' ' . $postalcode); ?>
    </p>
    <p style="margin-bottom: 0;"><strong>Status:</strong> Pending Confirmation</p>
</div>

<div style="text-align: center; margin-top: 30px;">
    <a href="<?php echo htmlspecialchars($ordersUrl); ?>" class="button">View Order History</a>
</div>
