<?php
// Determine HTTP status code
$code = isset($errorCode) ? intval($errorCode) : 404;
if (isset($_SERVER['REDIRECT_STATUS']) && $_SERVER['REDIRECT_STATUS'] != 200) {
    $code = intval($_SERVER['REDIRECT_STATUS']);
}
if (isset($_GET['code'])) {
    $code = intval($_GET['code']);
}

// Ensure code is one of the supported ones
$allowedCodes = [400, 401, 403, 404, 405, 408, 409, 413, 429, 500, 501, 502, 503, 504];
if (!in_array($code, $allowedCodes)) {
    $code = 404;
}

// Set HTTP response code if headers not sent
if (!headers_sent()) {
    http_response_code($code);
}

// Error metadata mapping
$errors = [
    400 => [
        'title' => 'Bad Request',
        'subtitle' => 'The server could not understand your request.',
        'desc' => 'The request could not be understood or processed by the server due to malformed syntax, invalid parameters, or size limitations.',
        'icon' => 'fa-bomb',
        'color' => '#ff4d6d' // red
    ],
    401 => [
        'title' => 'Access Unauthorized',
        'subtitle' => 'Authentication is required for this area.',
        'desc' => 'You are trying to access a secure resource that requires prior authentication. Please log in with valid credentials.',
        'icon' => 'fa-key',
        'color' => '#d7b46a' // gold
    ],
    403 => [
        'title' => 'Access Forbidden',
        'subtitle' => 'You do not have permission here.',
        'desc' => 'Your request was understood, but the server is refusing to fulfill it. Access to this directory or file is strictly restricted.',
        'icon' => 'fa-lock',
        'color' => '#ff4d6d'
    ],
    404 => [
        'title' => 'Page Not Found',
        'subtitle' => 'The requested URL was not found.',
        'desc' => 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable. Let\'s get you back on track.',
        'icon' => 'fa-compass',
        'color' => '#d7b46a'
    ],
    405 => [
        'title' => 'Method Not Allowed',
        'subtitle' => 'Requested method is not supported.',
        'desc' => 'The request method is not allowed for the requested URL. Please check your request method or headers.',
        'icon' => 'fa-ban',
        'color' => '#ff4d6d'
    ],
    408 => [
        'title' => 'Request Timeout',
        'subtitle' => 'The server timed out waiting.',
        'desc' => 'The server did not receive a complete request within the time limit set. This is usually due to network congestion or slow client connection.',
        'icon' => 'fa-hourglass-end',
        'color' => '#e8dcc8'
    ],
    409 => [
        'title' => 'Request Conflict',
        'subtitle' => 'A resource conflict has occurred.',
        'desc' => 'The request could not be completed due to a conflict with the current state of the server. This often occurs during concurrent updates.',
        'icon' => 'fa-code-branch',
        'color' => '#ff4d6d'
    ],
    413 => [
        'title' => 'Payload Too Large',
        'subtitle' => 'The upload size limit was exceeded.',
        'desc' => 'The request is larger than what the server is willing or able to process. This usually happens when uploading an excessively large file.',
        'icon' => 'fa-file-archive',
        'color' => '#ff4d6d'
    ],
    429 => [
        'title' => 'Too Many Requests',
        'subtitle' => 'Rate limit exceeded.',
        'desc' => 'You have sent too many requests in a given amount of time. Please slow down and let the server rest before sending more requests.',
        'icon' => 'fa-gauge-high',
        'color' => '#98ff4a' // neon green
    ],
    500 => [
        'title' => 'Internal Server Error',
        'subtitle' => 'Something went wrong on our end.',
        'desc' => 'The server encountered an internal error or misconfiguration and was unable to complete your request. Our developers have been notified.',
        'icon' => 'fa-circle-exclamation',
        'color' => '#ff4d6d'
    ],
    501 => [
        'title' => 'Not Implemented',
        'subtitle' => 'Requested action is not supported.',
        'desc' => 'The server does not support the functionality required to fulfill the request. The requested feature is not yet built.',
        'icon' => 'fa-screwdriver-wrench',
        'color' => '#e8dcc8'
    ],
    502 => [
        'title' => 'Bad Gateway',
        'subtitle' => 'Upstream server connection failure.',
        'desc' => 'The server, while acting as a gateway or proxy, received an invalid response from the upstream server it accessed while attempting to fulfill the request.',
        'icon' => 'fa-network-wired',
        'color' => '#ff4d6d'
    ],
    503 => [
        'title' => 'Service Unavailable',
        'subtitle' => 'The server is temporarily offline.',
        'desc' => 'The server is temporarily unable to handle your request due to maintenance, temporary overloading, or system capacity limits. Please try again soon.',
        'icon' => 'fa-server',
        'color' => '#e8dcc8'
    ],
    504 => [
        'title' => 'Gateway Timeout',
        'subtitle' => 'The upstream connection timed out.',
        'desc' => 'The server, while acting as a gateway or proxy, did not receive a timely response from the upstream server it needed to access to complete the request.',
        'icon' => 'fa-wifi',
        'color' => '#e8dcc8'
    ]
];

$error = $errors[$code];

// Dynamically determine the base URL directory path of the app
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
if (substr($script_dir, -1) !== '/') {
    $script_dir .= '/';
}
$baseUrl = $protocol . $host . $script_dir;

// Generate specific fallback paths to be absolutely safe
$cssBootstrap = $baseUrl . "assets/css/bootstrap.min.css";
$cssFontAwesome = $baseUrl . "assets/css/all.min.css";
$cssGlobal = $baseUrl . "assets/css/styles.css";
$jsJquery = $baseUrl . "assets/js/jquery.js";
$jsFontAwesome = $baseUrl . "assets/js/all.min.js";
$jsBootstrap = $baseUrl . "assets/js/bootstrap.bundle.min.js";
$logoImg = $baseUrl . "assets/img/ma_trends_ill.webp";
$homeLink = $baseUrl . "index";
$shopLink = $baseUrl . "shop";
$loginLink = $baseUrl . "login";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex, nofollow" />
  <title><?php echo $code; ?> - <?php echo htmlspecialchars($error['title']); ?> | MA Trends</title>
  
  <link rel="shortcut icon" href="<?php echo $logoImg; ?>" type="image/x-icon">
  
  <!-- Google Fonts (Poppins + Inter) -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&family=Great+Vibes&display=swap" rel="stylesheet" />
  
  <!-- CSS Links (Robust Base Paths) -->
  <link href="<?php echo $cssFontAwesome; ?>" rel="stylesheet" />
  <link href="<?php echo $cssBootstrap; ?>" rel="stylesheet" />
  <link href="<?php echo $cssGlobal; ?>" rel="stylesheet" />

  <style>
    /* Custom style tweaks specifically for error pages */
    .error-card {
      min-height: 480px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      padding: 3rem 2rem;
      border: 1px solid var(--ma-border);
      border-radius: var(--ma-radius);
      background: linear-gradient(180deg, rgba(17, 19, 25, 0.95), rgba(11, 12, 15, 0.98));
      position: relative;
      overflow: hidden;
      margin-top: 2rem;
      margin-bottom: 2rem;
      box-shadow: var(--ma-shadow);
    }
    
    .error-card::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 2px;
      background: linear-gradient(90deg, transparent, <?php echo $error['color']; ?>, transparent);
      opacity: 0.8;
    }

    .error-code {
      font-size: clamp(5rem, 10vw, 8rem);
      font-weight: 800;
      line-height: 1;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, <?php echo $error['color']; ?>, #f4f1ea, <?php echo $error['color']; ?>);
      background-size: 200% auto;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: codeGradient 6s ease infinite;
      margin-bottom: 1rem;
    }

    .error-icon {
      font-size: 3.5rem;
      color: <?php echo $error['color']; ?>;
      margin-bottom: 1rem;
      animation: floatIcon 3s ease-in-out infinite;
    }

    @keyframes floatIcon {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }

    @keyframes codeGradient {
      0% { background-position: 0% center; }
      50% { background-position: 200% center; }
      100% { background-position: 0% center; }
    }
    
    /* Make sure html/body covers viewport and navbar stays fixed */
    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    
    main {
      flex: 1 0 auto;
      display: flex;
      align-items: center;
    }
    
    .ma-footer {
      flex-shrink: 0;
    }
  </style>
</head>
<body>
  <main>
      <div class="container">
          <div class="row justify-content-center">
              <div class="col-12 col-md-8 col-lg-7">
                  <div class="error-card">
                      <div class="error-icon">
                          <i class="fas <?php echo $error['icon']; ?>"></i>
                      </div>
                      <div class="error-code"><?php echo $code; ?></div>
                      <h1 class="h3 fw-bold mb-2"><?php echo htmlspecialchars($error['title']); ?></h1>
                      <h2 class="h6 fw-semibold color-gold mb-3"><?php echo htmlspecialchars($error['subtitle']); ?></h2>
                      <p class="ma-muted mb-4 small px-md-4">
                          <?php echo htmlspecialchars($error['desc']); ?>
                      </p>
                      
                      <!-- Context details for debugging -->
                      <?php if (isset($_SERVER['REQUEST_URI']) && $code != 500 && $code != 503): ?>
                          <div class="alert alert-dark border-0 ma-bg-surface-2 ma-muted py-2 px-3 mb-4 rounded-3 small text-break">
                              Requested path: <code><?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?></code>
                          </div>
                      <?php endif; ?>
                      
                      <div class="d-flex flex-wrap gap-2 justify-content-center">
                          <a href="<?php echo $homeLink; ?>" class="btn btn-ma px-4">
                              <i class="fas fa-home me-2"></i>Return Home
                          </a>
                          <a href="<?php echo $shopLink; ?>" class="btn btn-ma-outline px-4">
                              <i class="fas fa-shopping-bag me-2"></i>Visit Shop
                          </a>
                          <button onclick="history.back()" class="btn btn-ma-ghost px-3">
                              <i class="fas fa-arrow-left me-1"></i>Go Back
                          </button>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </main>

  <!-- Scripts -->
  <script src="<?php echo $jsJquery; ?>"></script>
  <script src="<?php echo $jsFontAwesome; ?>"></script>
  <script src="<?php echo $jsBootstrap; ?>"></script>
</body>
</html>
