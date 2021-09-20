<?php namespace Rugosa;

/*--
name=setup;
friendly_name=Rugosa Setup;
description=Setup wizard
--*/

$r->setup_web_directory = $r->plugin->dirurl;
$r->setup_complete_flag = file_exists(Path::combine($r->plugin->dir, ".complete"));

$r->hook('preload', function() use ($r) {
    if (!$r->setup_complete_flag) {
        Header('Location: ' . $r->setup_web_directory);
    }
})

?>