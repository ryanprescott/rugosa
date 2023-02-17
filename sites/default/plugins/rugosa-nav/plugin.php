<?php
namespace RugosaNav;
/*--
name=rugosa-nav
friendly_name=RugosaNav
description=Builds a navigation bar using your pages.
--*/

function build() {
	if (defined('\Rugosa\pages') && \Rugosa\pages instanceof \Rugosa\Collection) {
		foreach (\Rugosa\pages->items() as $page) {
			$active = \Rugosa\page->name === $page->name;
			$classAttr = $active ? ' class="active"' : '';
			if ($page->title && $page->name && !$page->hidden) {
				$route = \Rugosa\Path::combine(__WEBROOT__, $page->name);
				echo <<<NAVLINK
					<a href="$route"><li$classAttr>$page->title</li></a>
				NAVLINK;
			}
		}
	}
}

\Rugosa\hooks->set('rugosa_nav', new \Rugosa\Hook());
\Rugosa\hook('rugosa_nav', function () { build(); });

?>
