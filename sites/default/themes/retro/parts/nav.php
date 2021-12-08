<?php
	scandir("/");
?>
<div class="simple-nav">
	<ul>
		<a href="/"><li><strong><?=$r->site->title?></strong></li></a>
		<?php 
			$r->nav->build($r->pages); 
		?>
		
	</ul>
</div>