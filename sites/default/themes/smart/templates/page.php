<?php
/*--
name=page
description=Default page
--*/
?>
<!DOCTYPE html>
<html>
<?php include(Rugosa\theme->dir . "/parts/head.php"); ?>
<body>
<?php include(Rugosa\template->dir . "/../parts/header.php"); ?>
<?php include(Rugosa\template->dir . "/../parts/nav.php"); ?>
<div id="content">
	<?php Rugosa\render_content(); ?>
</div>
<?php include(Rugosa\template->dir . "/../parts/footer.php"); ?>
</body>
</html>