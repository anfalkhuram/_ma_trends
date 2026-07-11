<?php
// base_template.php
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MATrends</title>
    <style>
        body { margin: 0; padding: 0; font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f5f7; color: #333333; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f4f5f7; padding: 40px 0; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; color: #333333; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background-color: #ffffff; padding: 30px; text-align: center; border-bottom: 1px solid #eaebed; }
        .header h1 { margin: 0; font-size: 28px; color: #111111; letter-spacing: 1px; font-weight: 800; }
        .content { padding: 40px 30px; line-height: 1.6; font-size: 16px; }
        .footer { background-color: #111111; color: #ffffff; text-align: center; padding: 30px; font-size: 14px; }
        .footer a { color: #cccccc; text-decoration: underline; }
        .button { display: inline-block; padding: 14px 30px; background-color: #111111; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: 600; margin-top: 20px; letter-spacing: 0.5px; }
        h2 { color: #111111; margin-top: 0; font-weight: 700; font-size: 22px; }
        p { margin: 0 0 16px 0; color: #555555; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 20px; margin-bottom: 20px; }
        .data-table th, .data-table td { padding: 12px; border-bottom: 1px solid #eaebed; text-align: left; }
        .data-table th { background-color: #f8f9fa; color: #444; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .highlight-box { background-color: #f8f9fa; border: 1px solid #eaebed; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0; }
        .highlight-text { font-size: 28px; font-weight: 800; color: #111111; letter-spacing: 4px; margin: 0; }
    </style>
</head>
<body>
    <center class="wrapper">
        <table class="main" width="100%">
            <tr>
                <td class="header">
                    <h1>MATrends</h1>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <?php echo $emailContent; ?>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    <p style="color: #999; margin-bottom: 10px;">&copy; <?php echo date('Y'); ?> MATrends. All rights reserved.</p>
                    <p style="color: #999; margin-bottom: 0;">Need help? Contact us at <a href="mailto:team@matrends.store">team@matrends.store</a></p>
                </td>
            </tr>
        </table>
    </center>
</body>
</html>
