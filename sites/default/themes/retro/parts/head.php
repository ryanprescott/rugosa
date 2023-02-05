<?php
Rugosa\hook('head_tag', function() { ?>
    <meta name="viewport" content="user-scalable=0, width=device-width, initial-scale=1.0, minimal-ui" />
    <link rel="stylesheet" type="text/css" href="<?=Rugosa\theme->dirurl?>/styles/retro.css"><?php
});

Rugosa\hook('head_tag', function () { Rugosa\title_tag(); });

Rugosa\hook('head_tag');
?>
