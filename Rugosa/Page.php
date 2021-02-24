<?php namespace Rugosa;
class Page extends Readonly {
	protected string $name;
	protected string $title;
	protected string $description;
	protected string $theme;
	protected string $template;
	protected $content;
	protected string $redirect;
	protected string $path;
	protected int $nav_order;
	protected bool $hidden;
}
?>