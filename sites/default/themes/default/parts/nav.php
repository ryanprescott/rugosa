<?php
if (Rugosa\plugins->has('rugosa-nav')) { 
?>

<input type="checkbox" id="navtoggle">
<div id="nav">
<ul id="navitems">
<label for="navtoggle"><li>&larr;</li></label>
<?php
    RugosaNav\Nav::build(Rugosa\pages);
?>
</ul>
</div>

<?php
}
?>