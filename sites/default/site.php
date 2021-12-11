<?php
/*--
name=default;
title=Rugosa Web Framework;
theme=default
--*/

/* You can put site specific code here */

$r->logo = function () use ($r) {
    return '<span class="rugosa"></span>&nbsp;' . $r->site->title;
}

?>
