<h2>New Login Detected</h2>
<p>Hello <?php echo htmlspecialchars($name); ?>,</p>
<p>We noticed a successful login to your MATrends account on <strong><?php echo date('M d, Y g:i A'); ?></strong>.</p>
<div class="highlight-box" style="padding: 15px;">
    <p style="margin:0; font-size:14px; color:#555;">If this was you, there is nothing you need to do.</p>
</div>
<p style="color: #d9534f; font-size: 14px; font-weight: bold;">If this was NOT you, please <a href="<?php echo htmlspecialchars($resetUrl); ?>" style="color: #d9534f;">reset your password</a> immediately and contact our support.</p>
