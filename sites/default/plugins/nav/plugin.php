<?php namespace Rugosa;
/*--
name=nav;
friendly_name=Nav;
description=Builds a navigation bar using your pages.;
--*/

class Nav {
	public function build($object = null) {
		if ($object instanceof Collection) {
			foreach ($object->items as $page) {
				if ($page->title && $page->name && !$page->hidden) {
?>
<a href="<?=Path::combine(__WEBROOT__, $page->name)?>"><li><?=$page->title?></li></a>
<?php
				}
			}
		}
	}
}

if ($r->hooks->before_render_page) {
	$r->hooks->before_render_page->add(function () use ($r) {
		if (!$r->pages) {
			$r->load_pages();
		}
		$r->nav = new Nav;
	});
}

?>
