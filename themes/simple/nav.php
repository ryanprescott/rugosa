<?php
?>
<div id="nav">
				<ul id="navitems">
					<?php 
						if ($r->nav) { 
							$r->nav->build($r->pages); 
						} 
					?>
				</ul>
</div>