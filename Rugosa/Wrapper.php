<?php namespace Rugosa;

class Wrapper {
	public function __get($name) {
		return $this->{$name} ?? null;
	}

	public function __call($name, $arguments) {
		return is_callable($this->{$name}) ? ($this->{$name})(...$arguments) : null;
	}
	
	public function __set($name, $value) {
		$this->{$name} = $value;
	}
}

?>