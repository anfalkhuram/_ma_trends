<h2>New Order Received!</h2>
<p>A new order has been placed on MATrends.</p>

<div style="background-color: #f8f9fa; padding: 15px; border-radius: 6px; margin-top: 20px;">
    <h3 style="margin-top:0; font-size:16px;">Order Summary</h3>
    <p style="margin-bottom: 5px;"><strong>Order ID:</strong> ORD-<?php echo htmlspecialchars($orderId); ?></p>
    <p style="margin-bottom: 5px;"><strong>Customer:</strong> <?php echo htmlspecialchars($name); ?> (<?php echo htmlspecialchars($phone); ?>)</p>
    <p style="margin-bottom: 5px;"><strong>Email:</strong> <?php echo htmlspecialchars($customerEmail); ?></p>
    <p style="margin-bottom: 5px;"><strong>Total:</strong> Rs. <?php echo number_format($total, 2); ?></p>
    <p style="margin-bottom: 5px;"><strong>Payment:</strong> <span style="text-transform:uppercase;"><?php echo htmlspecialchars($paymentMethod); ?></span></p>
</div>

<p style="margin-top:20px;"><strong>Shipping Address:</strong><br>
<?php echo htmlspecialchars($address); ?><br>
<?php echo htmlspecialchars($city . ', ' . $region . ' ' . $postalcode); ?></p>

<p><strong>Ordered Items:</strong></p>
<ul style="color:#555; line-height:1.6;">
    <?php foreach($items as $item): ?>
    <li><?php echo htmlspecialchars($item['product_name']); ?> (x<?php echo (int)$item['qty']; ?>)</li>
    <?php endforeach; ?>
</ul>

<div style="text-align: center; margin-top: 30px;">
    <a href="<?php echo htmlspecialchars($adminOrdersUrl); ?>" class="button">Manage Orders</a>
</div>
