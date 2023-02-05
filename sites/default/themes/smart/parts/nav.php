<?php
if (Rugosa\hooks->has('rugosa_nav')) { 
?>

<div id="nav">
<ul id="navitems">
<?php
    Rugosa\hook('rugosa_nav');
?>
</ul>
</div>

<?php
}
?>