<?php namespace RugosaSEO;
/*--
name=rugosa-seo
friendly_name=Rugosa SEO
--*/

\Rugosa\hook('after_select_page', function () {
    $metadata = [
        "title" => \Rugosa\title_string(),
        "description" => \Rugosa\page->description,
        "canonical" => \Rugosa\get_canonical(),
    ];
    
    \Rugosa\hook('head_tag', function() use($metadata) {
        echo <<<SEO
            <link rel="canonical" href="{$metadata['canonical']}">\n
        SEO;
        if ($metadata['title']) {
            echo <<<SEO
                <meta name="og:title" content="{$metadata['title']}">\n
            SEO;
        }
        if ($metadata['description']) {
            echo <<<SEO
                <meta name="description" content="{$metadata['description']}">\n
            SEO;
        }
    });
})

?>