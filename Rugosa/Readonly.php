<?php namespace Rugosa;
class Readonly {
	protected $file;
	protected $dir;
	protected $fileurl;
	protected $filedir;
	public function __construct($array = []) {
		foreach($array as $key=>$value) {
			$this->$key = $value;
		}
	}
    public function __get($name) {
		return $this->$name ?? null;
	}
	public function __set($name, $value) {
		return false;
	}
	public function __isset($name) {
		return isset($this->$name);
	}
	public function __toString() {
		return $this->name;
	}
}
?>