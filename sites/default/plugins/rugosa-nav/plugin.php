<?php
namespace RugosaNav;
/*--
name=rugosa-nav
friendly_name=RugosaNav
description=Builds a navigation bar using your pages.
--*/

class Nav {
	public static function build() {
		if (defined('\Rugosa\pages') && \Rugosa\pages instanceof \Rugosa\Collection) {
			foreach (\Rugosa\pages->items() as $page) {
				if ($page->title && $page->name && !$page->hidden) {
?>
<a href="<?=\Rugosa\Path::combine(__WEBROOT__, $page->name)?>"><li><?=$page->title?></li></a>
<?php
				}
			}
		}
	}
}

\Rugosa\hooks->set('rugosa_nav', new \Rugosa\Hook());
\Rugosa\hook('rugosa_nav', function () { Nav::build(); });

?>
