<?php
/*--
name=nav;
friendly_name=Nav;
description=Builds a navigation bar using your pages.;
--*/

class Nav {
	public function build($object = null) {
		if ($object instanceof Rugosa\Collection) {
			foreach ($object->items() as $page) {
				if ($page->title && $page->name && !$page->hidden) {
?>
<a href="<?=Rugosa\Path::combine(__WEBROOT__, $page->name)?>"><li><?=$page->title?></li></a>
<?php
				}
			}
		}
	}
}

$custom = Rugosa\custom;
$custom->nav = new Nav();
?>
