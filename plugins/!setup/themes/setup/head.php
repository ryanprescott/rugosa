<?php
$r->hooks->head_tag->add(function() use ($r) { ?>
<script>
    if (window.location.hash !== "") window.location.hash = ""
</script>
<meta name="viewport" content="user-scalable=0, width=device-width, initial-scale=1.0, minimal-ui" />
<link rel="stylesheet" type="text/css" href="<?=$r->theme->dirurl?>/styles/default.css">
<?php $r->title_tag(); }); 
$r->head_tag();
?>
