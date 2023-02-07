<?php
namespace Rugosa;
function panic($errno = null, $errstr = null, $errfile = null, $errline = null) {
while (ob_get_level()) { ob_end_clean(); }
http_response_code('500');

$undefinedConstantPrefix = 'Undefined constant';
$isUndefinedConstant = substr($errstr, 0, strlen($undefinedConstantPrefix)) === $undefinedConstantPrefix;

if ($isUndefinedConstant) {
    $rugosaNamespacePrefix = 'Rugosa\\';

    preg_match('/"([^"]+)"/', $errstr, $matches);
    [, $constantName] = $matches ?: [];
    if (substr($constantName, 0, strlen($rugosaNamespacePrefix))) {
        [, $normalizedName] = explode('\\', $constantName);
        switch($normalizedName) {
            case 'sites':
                $errstr .= '. Did you forget to put your code in the `after_load_sites` hook?';
                break;
            case 'pages':
                $errstr .= '. Did you forget to put your code in the `after_load_pages` hook?';
                break;
            case 'site':
                $errstr .= '. Did you forget to put your code in the `after_select_site` hook?';
                break;
            case 'page':
                $errstr .= '. Did you forget to put your code in the `after_select_page` hook?';
                break;
            default:
                
        }
    }
}

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