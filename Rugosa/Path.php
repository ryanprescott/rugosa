<?php namespace Rugosa;

class Path {
	static function normalize($path) {
		$path = str_replace('\\', '/', $path);
		$path = preg_replace("#(?!^)/+#", "\\1/", $path);
		$path = rtrim($path, '/');
		return $path;
	}	
	static function combine() {
		// Group path elements with directory separator.
		$path = implode('/', func_get_args());
		$path = self::normalize($path);
		return $path;
	}	
	static function split($path) {
		return explode('/', self::normalize($path));
	}	
	static function diff($path1, $path2) {
	// Subtract one path from another.
		$path1 = self::normalize($path1);
		$path2 = self::normalize($path2);
		$index = stripos($path1, $path2);
		if ($index !== false) {
			return self::normalize(str_replace($path2, '', $path1));
		} else {
			return $path1;
		}
	}
	static function up($path) {
		$split = self::split($path);
		array_pop($split);
		return self::combine(...$split);
	}
}

?>