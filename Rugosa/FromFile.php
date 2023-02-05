<?php namespace Rugosa;
class FromFile {
	public readonly ?string $file;
	public readonly ?string $dir;
	public readonly ?string $fileurl;
	public readonly ?string $dirurl;

	public function __construct($array = []) {
		$r = new \ReflectionClass($this);
		$props = $r->getProperties();
		foreach($props as $prop) {
			$key = $prop->name;
			if (isset($array[$key])) {
				$this->$key = $array[$key];
			} else {
				$this->$key = null;
			}
		}
	}

	public function __get($name) {
		return $this->{$name} ?? null;
	}
}
?>