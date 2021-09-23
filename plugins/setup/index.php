<?php namespace Rugosa;

@require_once($_SERVER['DOCUMENT_ROOT']. '/Rugosa/Core.php');

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

$r->pages = new Collection();
$r->pages->add(new Page([
    "name"=>"setup",
    "content"=>$r->setup_content
]));

$r->themes = new Collection();
$r->themes->add(new Theme([
    "name"=>"setup",
    "dir"=>__DIR__
]));

$r->use_default_styles();
$r->select_theme("setup");
$r->select_page("setup");

$r->logo = "<h1><span class='rugosa'></span> Rugosa</h1>";

$r->render_page();

?>