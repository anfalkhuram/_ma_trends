<h2>Your Order has been Delivered!</h2>
<p>Hello <?php echo htmlspecialchars($name); ?>,</p>
<p>Great news! Your order <strong>#ORD-<?php echo htmlspecialchars($orderId); ?></strong> has been delivered.</p>
<p>We hope you love your new items! If you have any issues with your order, please do not hesitate to contact our support team.</p>
<div style="text-align: center; margin-top: 30px;">
    <a href="<?php echo htmlspecialchars($shopUrl); ?>" class="button">Shop Again</a>
</div>
