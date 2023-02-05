<?php namespace Rugosa;
class Theme extends FromFile {
	public string $name;
	public ?string $friendly_name;
	public ?string $description;
	public ?string $version;
	public ?string $path;
	public ?string $default_template;
	public ?Collection $templates;
}
?>