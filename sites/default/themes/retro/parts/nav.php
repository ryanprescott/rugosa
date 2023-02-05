<?php if (Rugosa\hooks->has('rugosa_nav')): ?>
<div class="simple-nav">
	<ul>
		<a href="/"><li><strong><?=Rugosa\site->title?></strong></li></a>
		<?php
			Rugosa\hook('rugosa_nav');
		?>
		
	</ul>
</div>
<?php endif; ?>