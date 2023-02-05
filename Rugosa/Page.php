<?php namespace Rugosa;
class Page extends FromFile {
	public ?string $name;
	public ?string $title;
	public ?string $description;
	public ?string $theme;
	public ?string $template;
	public string|\Closure|null $content;
	public ?string $redirect;
	public ?string $path;
	public ?int $nav_order;
	public ?bool $hidden;
}
?>