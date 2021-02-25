<?php namespace Rugosa;
/*--
name=debug;
friendly_name=Debug;
description=Quick stats
--*/

if ($r->hooks->before_render_page || $r->hooks->after_render_page || $r->hooks->after_render_content || $r->hooks->head_tag) {

$r->hook("head_tag", '<script>
var howfast_client_start = window.performance.now() || Date.now();
</script>
<style>
#howfast_display{font-size: 8pt; font-style: oblique; transition: opacity 0.5s; opacity: 0;}
#howfast_display.ready{opacity: 1;}
</style>');

$r->hook('before_render_page', function() use ($r) {
	$r->howfast_start = microtime(true);
});

$r->hook('after_render_content', '<p id=\'howfast_display\'>&nbsp;</p>');

$r->hook('after_render_page', function() use ($r) {
	$r->howfast_end = microtime(true);
	$r->howfast_elapsed = sprintf('%.5f', $r->howfast_end - $r->howfast_start);
?>
<script>var howfast_server_elapsed = <?=$r->howfast_elapsed?></script>
<?php
});

$r->hook('after_render_page', "<script type='text/javascript'>
window.onload = function() {
var howfast_client_end = window.performance.now() || Date.now();
var howfast_client_elapsed = (howfast_client_end - howfast_client_start) / 1000;
var howfast_client_elapsed_styled = Number.parseFloat(howfast_client_elapsed).toFixed(5);
var howfast_display = document.getElementById('howfast_display');
	if (howfast_display) {
		howfast_display.innerHTML = 'Server completed all tasks in ' + howfast_server_elapsed + ' seconds. Page loaded on client in ' + howfast_client_elapsed_styled + ' seconds.';
		howfast_display.classList.add('ready');
	}
}
</script>");

} else {
	trigger_error("Howfast: one or more hooks unavailable");
}

?>