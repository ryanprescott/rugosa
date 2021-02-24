<?php
$r->hooks->head_tag->add(function() use ($r) { ?>
<meta name="viewport" content="user-scalable=0, width=device-width, initial-scale=1.0, minimal-ui" />
<link rel="stylesheet" type="text/css" href="/themes/default/styles/default.css">
<?php $r->title_tag(); }); 
$r->head_tag();
?>
