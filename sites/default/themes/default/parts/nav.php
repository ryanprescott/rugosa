<?php
if (isset($nav)) { 
?>

<input type="checkbox" id="navtoggle">
<div id="nav">
<ul id="navitems">
<label for="navtoggle"><li>&larr;</li></label>
<?php 
$nav->build(Rugosa\pages); 
?>
</ul>
</div>

<?php
}
?>