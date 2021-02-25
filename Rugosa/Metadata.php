<?php namespace Rugosa;
class Metadata {
const open = '/*--';
const close = '--*/';
static function from_string($string) {
    $begin = strpos($string, self::open);
    $end = strpos($string, self::close);
    if ($begin !== false && $end !== false && $begin < $end) {
        $innerBegin = $begin + strlen(self::open);
        $innerEnd = $end - $begin - strlen(self::close);
        $block = trim(substr($string, $innerBegin, $innerEnd));
        return parse_ini_string($block);
    }
    return false;
}
static function from_php_string($string) {
    foreach(token_get_all($string) as $token) {
        if (is_array($token) && $token[0] == T_COMMENT) { 
            return self::from_string($token[1]); 
        }
    }
}
static function from_php_file($path) {
    if (file_exists($path)) {
        $string = file_get_contents($path);
        $block = self::from_php_string($string);
        $block['file'] = realpath($path);
        $block['dir'] = dirname($block['file']);
        $block['fileurl'] = Path::combine(__WEBROOT__, Path::diff($block['file'], __DOCROOT__));
        $block['dirurl'] = Path::combine(__WEBROOT__, Path::diff($block['dir'], __DOCROOT__));
        return $block;
    } else {
        return false;
    }
}
}
?>