<?php
if (true) { 
?>

<input type="checkbox" id="navtoggle">
<div id="nav">
<ul id="navitems">
<label for="navtoggle"><li>&larr;</li></label>
<?php
    Rugosa\custom->nav->build(Rugosa\pages);
?>
</ul>
</div>

<?php
}
?>