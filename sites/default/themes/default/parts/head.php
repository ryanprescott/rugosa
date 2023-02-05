<?php
$hooks->head_tag->add(function() { global $theme; ?>
<meta name="viewport" content="user-scalable=0, width=device-width, initial-scale=1.0, minimal-ui" />
<link rel="stylesheet" type="text/css" href="<?=$theme->dirurl?>/styles/default.css">
<?php /* title_tag(); */ }); 
Rugosa\head_tag();
?>
