<h2>Password Changed Successfully</h2>
<p>Hello <?php echo htmlspecialchars($name); ?>,</p>
<p>Your MATrends account password was successfully changed on <strong><?php echo date('M d, Y g:i A'); ?></strong>.</p>
<p>You can now use your new password to log in.</p>
<div style="text-align: center; margin-top: 30px; margin-bottom: 20px;">
    <a href="<?php echo htmlspecialchars($loginUrl); ?>" class="button">Log In to MATrends</a>
</div>
<p style="color: #d9534f; font-size: 14px; font-weight: bold;">If you did not request this change, please contact our support team immediately.</p>
