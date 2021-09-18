<?php namespace Rugosa;
/*--
name=debug;
friendly_name=Debug;
description=Quick stats
--*/

$r->howfast_assets = Path::combine($r->plugin->dirurl, 'assets');

$r->hook("head_tag", '<script>var howfast_client_start = window.performance.now() || Date.now();</script>');
$r->hook("head_tag", '<link rel=\'stylesheet\' type=\'text/css\' href=\'' . $r->howfast_assets . '/css/styles.css\'>');

$r->hook('before_render_page', function() use ($r) {
	$r->howfast_start = microtime(true);
});

$r->hook('after_render_content', '<p id=\'howfast_display\'>&nbsp;</p>');

$r->hook('after_render_page', function() use ($r) {
	$r->howfast_end = microtime(true);
	$r->howfast_elapsed = sprintf('%.5f', $r->howfast_end - $r->howfast_start);
});

$r->hook('after_render_page', function() use ($r) {
?>
<script>var howfast_server_elapsed = <?=$r->howfast_elapsed?> </script>
<?php
});

$r->hook('after_render_page', '<script src=\'' . $r->howfast_assets . '/js/after_render_page.js\'></script>');

?>