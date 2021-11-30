<?php namespace Rugosa;

@require_once(__DIR__.'/../../Rugosa/Core.php');

$r->setup_template = function() use ($r) {
?>
<!DOCTYPE html>
<html>
<?php $r->hooks->head_tag->add(function() use ($r) { ?>
<script>
if (window.location.hash !== "") window.location.hash = ""
</script>
<meta name="viewport" content="user-scalable=0, width=device-width, initial-scale=1.0, minimal-ui" />
<style>
html{
    background-color: #222;
}
#main {
    margin: 5rem auto;
    max-width: 1024px;
    background-color: #fff;
    border-radius: 1rem;
    overflow: hidden;
}
.logo {
    display: inline-block;
}
div#content {
    padding: 0 1rem;
}
div#header {
    width: 100%;
    padding: 0px 1rem;
    box-sizing: border-box;
    background-color: #eee;
}
</style>
<?php $r->title_tag(); }); 
$r->head_tag();
?>
<body>
    <div id="main">
        <div id="header">
            <div class="logo"><?=$r->logo()?></div>
        </div>
        <div id="content">
            <?php $r->render_content(); ?>
        </div>
    </div>
</body>
</html>
<?php
};

$r->setup_content = function() use ($r) {
?>
<form method='post' action=''>
<input type="hidden" value="submitted">
<div class='panels'>
    <div class='panel' id='basic'>
        <h1>Basic Information</h1>
        <p>What do you want your site to be called?</p>
        <label for='title'>Site Title:</label>
        <input type='text' name='title' id='title' value='My Great Website'>
        <div class='rack'>
            <button>Finish</button>
        </div>
    </div>
    <div class='panel default' id='start'>
        <h1>Welcome to Rugosa!</h1>
        <p>
            We need to get a few things sorted before we can dive into the Rugosa experience. Click 'Next' to begin the setup process.
        </p>
        <div class='rack'>
            <a class='button' href='#basic'>Next</a>
        </div>
    </div>
</div>
</form>
<?php
};

$r->site = new Site([
    "name"=>"setup",
    "title"=>"Rugosa Setup"
]);

$r->page = new Page([
    "name"=>"setup",
    "content"=>$r->setup_content
]);

$r->theme = new Theme([
    "name"=>"setup",
]);

$r->template = new Template([
    "name"=>"setup",
    "content"=>$r->setup_template
]);

$r->use_default_styles();
$r->logo = "<h1><span class='rugosa'></span> Rugosa</h1>";

$r->render_page();

?>