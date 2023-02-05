<?php
if (Rugosa\hooks->has('rugosa_nav')) { 
?>

<input type="checkbox" id="navtoggle">
<div id="nav">
<ul id="navitems">
<label for="navtoggle"><li>&larr;</li></label>
<?php
    Rugosa\hooks->rugosa_nav->execute();
?>
</ul>
</div>

<?php
}
?>