<?php
/*--
name=default;
title=Rugosa Web Framework;
theme=default
--*/

/* You can put site specific code here */

$logo = function () {
    global $site;
    return '<span class="rugosa"></span>&nbsp;' . $site->title;
}

?>
