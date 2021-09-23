<!DOCTYPE html>
<html>
<?php
$r->hooks->head_tag->add(function() use ($r) { ?>
<script>
    if (window.location.hash !== "") window.location.hash = ""
</script>
<meta name="viewport" content="user-scalable=0, width=device-width, initial-scale=1.0, minimal-ui" />
<style>
	
html{
	background-color: #222;
}

body {
	margin: 5rem auto;
	max-width: 1024px;
	background-color: #fff;
	border-radius: 1rem;
	overflow: hidden;
	z-index: 1;
}

.logo {
	display: inline-block;
}

div#content {
	padding: 0 1rem;
}

div#header {
	width: 100%;
	padding: 0px 1rem;
	box-sizing: border-box;
	background-color: #eee;
}
</style>
<?php $r->title_tag(); }); 
$r->head_tag();
?>
<body>
<div>
	<div id="header">
	<div class="logo"><?=$r->logo()?></div>
	</div>
</div>
<div id="content">
	<?php $r->render_content(); ?>
</div>
</body>
</html>