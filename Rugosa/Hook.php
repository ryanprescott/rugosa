<?php namespace Rugosa;

class Hook {
	private array $items = [];

	public function add($item) {
		if (is_string($item) || $item instanceof \Closure) {
			array_push($this->items, $item);
			return true;
		} else {
			trigger_error("Hook->add: Items added to Hook must be a string or a closure.");
			return false;
		}
	}

	public function execute() {
		foreach ($this->items as $item) {
			if (is_callable($item)) {
				($item)();
			} else {
				echo($item);
			}
		}
	}

	public function __invoke() {
		$this->execute();
	}
}

?>