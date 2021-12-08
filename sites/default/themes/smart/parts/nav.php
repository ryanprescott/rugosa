<?php
if ($r->nav) { 
?>

<div id="nav">
<ul id="navitems">
<?php 
$r->nav->build($r->pages); 
?>
</ul>
</div>

<?php
}
?>