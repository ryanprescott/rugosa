<?php namespace Rugosa;

class Collection {

	private array $items = [];

	public function items() {
		return $this->items;
	}
	
	public function add($item) {
		if (!$item->name) {
			trigger_error("Collection->add: Item name cannot be empty.");
		} else {
			$this->set($item->name, $item);
		}
	}

	public function set($name, $item) {
		if (isset($this->items[$name])) {
			trigger_error("Collection->set: Item '{$item->name}' is already present in Collection.");
			return false;
		} else {
			$firstItemClass = (count($this->items) > 0) ? get_class(reset($this->items)) : false;
			if (!$firstItemClass || get_class($item) === $firstItemClass) {
				$this->items[$name] = $item;
				return true;
			} else {
				trigger_error("Collection->set: Items added to this Collection must be instance of " . $firstItemClass . ". This is determined by the first item added to the Collection.");
				return false;
			}
		}
	}
	
	public function get($name) {
		return $this->has($name) ? $this->items[$name] : null;
	}

	public function has($name) {
		return isset($this->items[$name]);
	}

	public function remove($name) {
		if (array_key_exists($name, $this->items)) {
			unset($this->items[$name]);
			return true;
		} else {
			trigger_error("Item '{$name}' does not exist in Collection.");
			return false;
		}
	}

	public function __get($name) {
		return $this->get($name);
	}
}

?>