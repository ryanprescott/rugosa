<?php
/*--
name=default;
title=Rugosa Web Framework;
theme=smart
--*/

/* You can put site specific code here */

$r->logo = function () use ($r) {
    return '<span class="rugosa"></span>&nbsp;' . $r->site->title;
}

?>
