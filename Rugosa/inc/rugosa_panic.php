<?php
namespace Rugosa;
function panic($errno = null, $errstr = null, $errfile = null, $errline = null) {
while (ob_get_level()) { ob_end_clean(); }
http_response_code('500');
?>
<html>
<head>
    <title>Rugosa</title>
</head>
<body style="background: #500;">
    <style>*{font-family:sans-serif}dialog{top:25%;background:#fff;padding:1rem;border-radius:6px;border:none;box-shadow:2px 2px 3px #000;max-width:800px;}</style>
    <dialog open>
        <h2><span class="rugosa"></span> Rugosa</h2>
        <p><strong>A serious error occurred in <?=$errfile ? "file '" . $errfile . "'" . ($errline ? " on line " . $errline : ""): "your Rugosa site"?>:</strong><br><?=$errstr ?? "The error message could not be displayed"?></p>
        <p>If you are the administrator of this site, it's possible that your site is misconfigured or a plugin is causing this error. Please check the error log to find out more information about what happened.</p>
    </dialog>
</body>
</html>
<?php
die();
}
?>