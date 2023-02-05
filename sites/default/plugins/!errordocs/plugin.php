<?php namespace Rugosa;
/*--
name=errordocs
friendlyname=Error Documents
description=Simple error documents without having to create any pages.
--*/

$r->hook("after_load_pages", function() use ($r) {
    $content_func = function() use ($r) {
        http_response_code($r->page->name);
        ?>
        <div class="hero">
        <h1><?=$r->page->title?></h1>
        <p><?=$r->page->description?></p>
        <p><a href="/">Go Home</a></p>
        <hr>
        </div>
        <?php
    };
    $skeletons = [
        ["404","404 Not Found","The page you are looking for does not exist."],
        ["500","500 Server Error","The server has encountered an error. Please try again later."]
    ];    
    foreach ($skeletons as $skeleton) {
        if (!isset($r->pages->items[$skeleton[0]])) {
            $arr = ["name"=>$skeleton[0],"title"=>$skeleton[1],"description"=>$skeleton[2],"content"=>$content_func,"hidden"=>true];
            $r->pages->add(new \Rugosa\Page($arr));
        }
    }
});
?>