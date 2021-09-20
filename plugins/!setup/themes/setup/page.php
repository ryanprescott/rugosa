<?php
/*--
name=page
description=Default page
--*/
?>
<!DOCTYPE html>
<html>
<?php include("head.php"); ?>
<body>
<?php include("header.php"); ?>
<?php include("nav.php"); ?>
<div id="content">
	<?php $r->render_content(); ?>
</div>
</body>
</html>