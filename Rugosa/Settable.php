<?php
namespace Rugosa;
class Settable {
    #[\AllowDynamicProperties]
    public function set(string $name, mixed $value) {
        $this->$name = $value;
    }
}

?>