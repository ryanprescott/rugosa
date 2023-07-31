<?php namespace Rugosa;
class Meta {
    public Plugin|null $_plugin;

    function get_plugin() {
        return clone $this->_plugin;
    }
}

?>