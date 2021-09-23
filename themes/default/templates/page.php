<?php
/*--
name=page
description=Default page
--*/
?>
<!DOCTYPE html>
<html>
<?php include($r->template->dir . "/../parts/head.php"); ?>
<body>
<?php include($r->template->dir . "/../parts/header.php"); ?>
<?php include($r->template->dir . "/../parts/nav.php"); ?>
<div id="content">
	<?php $r->render_content(); ?>
</div>
<?php include($r->template->dir . "/../parts/footer.php"); ?>
</body>
</html>