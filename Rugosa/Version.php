<?php namespace Rugosa;
class Version {
    public string $delimiter = '.';
    public int $major;
    public int $minor;
    public int $revision;
    public int $build;

    public function __toString() {
        return implode($this->delimiter, [$this->major, $this->minor, $this->revision, $this->build]);
    }

    public function __construct() {
        $args = func_get_args();
        $this->major = $args[0] ?? 0;
        $this->minor = $args[1] ?? 0;
        $this->revision = $args[2] ?? 0;
        $this->build = $args[3] ?? 0;
        $this->delimiter = $args[4] ?? '.';
    }
}
?>