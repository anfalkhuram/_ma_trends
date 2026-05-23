<?php
$codes = [401, 403, 404, 405, 408, 409, 413, 429, 500, 501, 502, 503, 504];
foreach ($codes as $code) {
    $content = "<?php\n\$errorCode = $code;\nrequire_once(__DIR__ . '/error.php');\n?>";
    file_put_contents(dirname(__DIR__) . "/$code.php", $content);
}
echo "Successfully created all error entry point files!";
?>
