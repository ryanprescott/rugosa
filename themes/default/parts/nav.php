<?php
if ($r->nav) { 
?>

<input type="checkbox" id="navtoggle">
<div id="nav">
<ul id="navitems">
<label for="navtoggle"><li>&larr;</li></label>
<?php 
$r->nav->build($r->pages); 
?>
</ul>
</div>

<?php
}
?>