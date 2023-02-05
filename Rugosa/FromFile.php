<?php namespace Rugosa;
class FromFile {
	public readonly string $file;
	public readonly string  $dir;
	public readonly string  $fileurl;
	public readonly string  $dirurl;

	public function __construct($array = []) {
		foreach($array as $key=>$value) {
			$this->$key = $value;
		}
	}

	public function __get($name) {
		return $this->{$name} ?? null;
	}
}
?>