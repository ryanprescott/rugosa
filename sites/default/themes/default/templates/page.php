<?php
/*--
name=page
description=Default page
--*/
?>
<!DOCTYPE html>
<html>
<?php include($template->dir . "/../parts/head.php"); ?>
<body>
<?php include($template->dir . "/../parts/header.php"); ?>
<?php include($template->dir . "/../parts/nav.php"); ?>
<div id="content">
	<?php Rugosa\render_content(); ?>
</div>
<?php include($template->dir . "/../parts/footer.php"); ?>
</body>
</html>