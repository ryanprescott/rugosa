<!DOCTYPE html>
<html>
	<?php include("head.php"); ?>
	<body>
		<div id="main">
			<?php include("header.php"); ?>
			<?php include("nav.php"); ?>
				<div id="content">
					<?php $r->render_content(); ?>
				</div>

			<?php include("footer.php"); ?>
		</div>
	</body>
</html>