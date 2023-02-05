<?php
/*--
name=page
description=Default page
--*/
?>
<!DOCTYPE html>
<html>
<?php include(Rugosa\template->dir . "/../parts/head.php"); ?>
<body>
<div id="main">
<?php include(Rugosa\template->dir . "/../parts/nav.php"); ?>
<div id="content">
	<?php Rugosa\render_content(); ?>
</div>
<?php include(Rugosa\template->dir . "/../parts/footer.php"); ?>
</div>
</body>
</html>

