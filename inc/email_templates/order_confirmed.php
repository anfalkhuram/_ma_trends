<h2>Your Order is Confirmed!</h2>
<p>Hello <?php echo htmlspecialchars($name); ?>,</p>
<p>Great news! Your order <strong>#ORD-<?php echo htmlspecialchars($orderId); ?></strong> has been confirmed by our team and is now being processed.</p>
<div class="highlight-box">
    <p style="margin:0; font-size:16px; font-weight:bold; color:#111;">Order Total: Rs. <?php echo number_format($total, 2); ?></p>
</div>
<p>We will notify you again once your order is out for delivery.</p>
<p>Thank you for shopping with MATrends!</p>
