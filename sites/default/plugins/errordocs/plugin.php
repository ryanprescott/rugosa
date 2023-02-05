<?php namespace RugosaErrorDocs;
/*--
name=rugosa_errordocs
friendlyname=Rugosa Error Documents
description=Simple error documents without having to create any pages.
--*/

\Rugosa\hook("after_load_pages", function() {
    $content_func = function() {
        http_response_code(\Rugosa\page->name);
        ?>
        <div class="hero">
        <h1><?=\Rugosa\page->title?></h1>
        <p><?=\Rugosa\page->description?></p>
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
        if (!isset(\Rugosa\pages->items[$skeleton[0]])) {
            $arr = ["name"=>$skeleton[0],"title"=>$skeleton[1],"description"=>$skeleton[2],"content"=>$content_func,"hidden"=>true];
            \Rugosa\pages->add(new \Rugosa\Page($arr));
        }
    }
});
?>