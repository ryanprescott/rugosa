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
        if ($r->get_selector_from_url() !== "setup") {
            Header('Location: /setup');
        }
        
        include_once($r->plugin->dir . 'setup.php');
        die();
    }
})

?>