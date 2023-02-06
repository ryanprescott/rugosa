<?php namespace Rugosa;

require_once('inc/rugosa_panic.php');
set_error_handler('Rugosa\panic', E_USER_ERROR);

set_exception_handler(function(\Throwable $ex) {
	panic($ex->getCode(), $ex->getMessage(), $ex->getFile(), $ex->getLine());
});

register_shutdown_function(function() {
	$error = error_get_last();
	if (is_array($error) && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR])) {
		panic($error['type'], $error['message'], $error['file'], $error['line']);
	}
});

spl_autoload_register(function($className) {
	if ($className === 'Rugosa\Core') {
		return;
	}

	$rewritten_path = __DIR__ . '/../'. str_replace('\\', '/', $className) . '.php';
	@require_once($rewritten_path);
});

if (PHP_VERSION[0]<8) {
	trigger_error("Rugosa is not compatible with PHP versions before 8.0. Please install the latest version of PHP.", E_USER_ERROR);
}

define('__DOCROOT__', $_SERVER['DOCUMENT_ROOT']);
define('__RELROOT__', Path::diff(getcwd(), __DOCROOT__));
define('__WEBRELROOT__', Path::combine('//',$_SERVER['HTTP_HOST'],__RELROOT__));
define('__WEBROOT__', Path::combine('//',$_SERVER['HTTP_HOST']));

const version = new Version(0, 23, 2, 0);

/*** Hooks ***/
const available_hooks = [
	'before_load_site',
	'after_load_site',
	'before_load_pages',
	'after_load_pages',
	'after_load_plugins',
	'before_select_page',
	'after_select_page',
	'before_render_page',
	'after_render_page',
	'before_render_content',
	'after_render_content',
	'preload',
	'postload',
	'head_tag',
];

const hooks = new Collection();
const sites = new Collection;
const themes = new Collection;
const pages = new Collection;
const templates = new Collection;
const plugins = new Collection;
const meta = new Meta();

foreach (available_hooks as $hook) {
	hooks->set($hook, new Hook());
}

function hook(string $hook, mixed $obj = null) {
	if (hooks->get($hook)) {
		if ($obj === null) {
			return hooks->{$hook}->execute();
		} else {
			return hooks->{$hook}->add($obj);
		}
	}
	return false;
};

function load_site(string $path) {
	if (!is_dir($path)) {
		trigger_error("load_site: Site could not be loaded. Supplied path '$path' does not exist or was not a directory.");
	}

	$def = Path::combine($path, "site.php");

	if (!file_exists($def)) {
		trigger_error("load_site: Site could not be loaded. File '{$def}' does not exist.");
	}

	$block = Metadata::from_php_file($def);

	if (!is_array($block)) {
		trigger_error("load_site: Site could not be loaded. File '{$def}' did not contain a valid site declaration.");
	}

	$site = new Site($block);

	if (!sites->add($site)) {
		trigger_error("load_site: Site '". $site->name . "' at '{$path}' could not be loaded either because it has no name, or it is a duplicate of an already loaded site.");
	}

	return true;
};

function load_sites() {
	$sitesDir = Path::combine(getcwd(), 'sites');
	if(!is_dir($sitesDir)) {
		trigger_error('load_sites: Sites directory does not exist or is not a directory.');
	}
	$siteDirs = array_diff(scandir($sitesDir), [".", ".."]);
	foreach($siteDirs as $siteDir) {
		if (substr($siteDir, 0, 1) !== "!") {
			$path = Path::combine($sitesDir, $siteDir);
			load_site($path);
		}
	}
};

function import_templates(string $path) {
	$templateCollection = new Collection;
	$templatesPath = Path::combine($path, "templates");
	if (is_dir($path)) {
		if (is_dir($templatesPath)) {
			$templateFiles = array_diff(scandir($templatesPath), [".", ".."]);
			foreach ($templateFiles as $templateFile) {
				$def = Path::combine($templatesPath, $templateFile);
				$block = Metadata::from_php_file($def);
				if (is_array($block)) {
					$template = new Template($block);
					$templateCollection->add($template);
				}
			}
		}
	}
	return $templateCollection;
};

function load_theme(string $path) {
	if (!is_dir($path)) {
		trigger_error("load_theme: Theme could not be loaded. Supplied path '$path' does not exist or was not a directory.");
	}

	$def = Path::combine($path, "theme.php");

	if (!file_exists($def)) {
		trigger_error("load_theme: Theme could not be loaded. File '{$def}' does not exist.");
	}

	$block = Metadata::from_php_file($def);

	if (!is_array($block)) {
		trigger_error("load_theme: Theme could not be loaded. File '{$def}' did not contain a valid theme declaration.");
	}

	
	$block['templates'] = import_templates($path);
	$theme = new Theme($block);

	if (!themes->add($theme)) {
		trigger_error("load_theme: Theme '". $theme->name . "' at '{$path}' could not be loaded either because it has no name, or it is a duplicate of an already loaded theme.");
	}

	return true;
};


function load_themes() {
	$themesDir = Path::combine(site->dir, 'themes');
	if(is_dir($themesDir)) {
		$themeDirs = array_diff(scandir($themesDir), [".", ".."]);
		foreach($themeDirs as $themeDir) {
			if (substr($themeDir, 0, 1) !== "!") {
				$path = Path::combine($themesDir, $themeDir);
				load_theme($path);
			}
		}
	} else {
		trigger_error('load_themes: Themes directory does not exist or is not a directory.');
		return false;
	}
};

function load_plugin(string $path) {
	if (!is_dir($path)) {
		trigger_error("load_plugin: plugin could not be loaded. Supplied path '$path' does not exist or was not a directory.");
	}

	$def = Path::combine($path, "plugin.php");

	if (!file_exists($def)) {
		trigger_error("load_plugin: Plugin could not be loaded. File '{$def}' does not exist.");
	}

	$block = Metadata::from_php_file($def);

	if (!is_array($block)) {
		trigger_error("load_plugin: Plugin could not be loaded. File '{$def}' did not contain a valid plugin declaration.");
	}

	$block['templates'] = import_templates($path);
	$plugin = new Plugin($block);

	$meta = meta;
	$meta->plugin = $plugin;
	include_once($def);
	$meta->plugin = null;

	if (!plugins->add($plugin)) {
		trigger_error("load_plugin: Plugin '". $plugin->name . "' at '{$path}' could not be loaded either because it has no name, or it is a duplicate of an already loaded plugin.");
	}

	return true;
};

function load_plugins() {
	$pluginsDir = Path::combine(site->dir, 'plugins');
	if(is_dir($pluginsDir)) {
		$pluginDirs = array_diff(scandir($pluginsDir), [".", ".."]);
		foreach($pluginDirs as $pluginDir) {
			if (substr($pluginDir, 0, 1) !== "!") {
				$path = Path::combine($pluginsDir, $pluginDir);
				load_plugin($path);
			}
		}
	} else {
		trigger_error('load_plugins: Plugins directory does not exist or is not a directory. If you do not expect to use');
		return false;
	}
};

function load_page(string $path) {
	if (!file_exists($path)) {
		trigger_error("load_site: Site could not be loaded. File '{$path}' does not exist.");
	}

	$block = Metadata::from_php_file($path);

	if (!is_array($block)) {
		trigger_error("load_site: Site could not be loaded. File '{$path}' did not contain a valid page declaration.");
	}

	$page = new Page($block);

	if (!pages->add($page)) {
		trigger_error("load_site: Site '". $page->name . "' at '{$path}' could not be loaded either because it has no name, or it is a duplicate of an already loaded page.");
	}

	return true;
};

function load_pages() {
	hook("before_load_pages");

	$pagesDir = Path::combine(site->dir, 'pages');

	if($pagesDir) {
		$pageFiles = array_diff(scandir($pagesDir), [".", ".."]);
		if (count($pageFiles) > 0) {
			foreach($pageFiles as $pageFile) {
				if (substr($pageFile, 0, 1) !== '!') {
					$path = Path::combine($pagesDir, $pageFile);
					load_page($path);
				}
			}
			hook("after_load_pages");
			return true;
		}
	}
	trigger_error('load_pages: Your site has no pages. Please create some pages before attempting to initialize Rugosa.', E_USER_ERROR);
};

function select_site() {
	$args = func_get_args();
	if ($args[0] instanceof Collection) {
		$siteCollection = array_shift($args);
	} elseif (sites instanceof Collection) {
		$siteCollection = sites;
	} else {
		trigger_error('The default site collection was not available and no collection was specified.', E_USER_ERROR);
		return false;
	}

	foreach($args as $selector) {
		if ($siteCollection->has($selector)) {
			define('Rugosa\site', $siteCollection->get($selector));
			break;
		}
	}

	if (site) {
		include_once(site->file);
		return true;
	} else {
		trigger_error('No site was found matching the selectors specified: ' . join(', ', $args), E_USER_ERROR);
		return false;
	}
};

function select_page() {
	$args = func_get_args();
	if ($args[0] instanceof Collection) {
		$pageCollection = array_shift($args);
	} elseif (pages instanceof Collection) {
		$pageCollection = pages;
	} else {
		trigger_error('The default page collection was not available and no collection was specified.', E_USER_ERROR);
		return false;
	}

	foreach($args as $selector) {
		if ($pageCollection->has($selector)) {
			define('Rugosa\page', $pageCollection->get($selector));
			break;
		}
	}

	if (defined('Rugosa\page')) {
		return true;
	} else {
		trigger_error('No page was found matching the selectors specified: ' . join(', ', $args), E_USER_ERROR);
		return false;
	}
};

function get_page_selector_from_url() {
	return trim(Path::diff(strtok($_SERVER['REQUEST_URI'], '?'), __RELROOT__), '/') ?: site->default_page ?: 'home';
};

function select_page_from_url(mixed $default = null) {
	$page_selector = get_page_selector_from_url();
	return select_page($page_selector, $default);
};

function get_site_selector_from_host() {
	return $_SERVER['HTTP_HOST'] ?: 'default';
};

function select_site_from_host(mixed $default = null) {
	$selector = get_site_selector_from_host();
	return select_site($selector, $default);
};

function select_theme() {
	$args = func_get_args();
	if ($args[0] instanceof Collection) {
		$themeCollection = array_shift($args);
	} elseif (themes instanceof Collection) {
		$themeCollection = themes;
	} else {
		trigger_error('The default theme collection was not available and no collection was specified.', E_USER_ERROR);
		return false;
	}

	foreach($args as $selector) {
		if ($themeCollection->has($selector)) {
			define('Rugosa\theme', $themeCollection->get($selector));
			break;
		}
	}

	if (defined('Rugosa\theme')) {
		return true;
	} else {
		trigger_error('No theme was found matching the selectors specified: ' . join(', ', $args), E_USER_ERROR);
		return false;
	}
};

function select_template() {
	$args = func_get_args();
	if ($args[0] instanceof Collection) {
		$templateCollection = array_shift($args);
	} elseif (theme->templates instanceof Collection) {
		$templateCollection = theme->templates;
	} else {
		trigger_error('The default template collection was not available and no collection was specified.', E_USER_ERROR);
		return false;
	}

	foreach($args as $selector) {
		if ($templateCollection->has($selector)) {
			define('Rugosa\template', $templateCollection->get($selector));
			break;
		}
	}

	if (defined('Rugosa\template')) {
		return true;
	} else {
		trigger_error('No template was found matching the selectors specified: ' . join(', ', $args), E_USER_ERROR);
		return false;
	}
};

function render_page() {
	if (defined('Rugosa\page') && page instanceof Page) {
		hooks->before_render_page->execute();

		if (!defined('Rugosa\template')) {
			select_template(page->template, site->template, theme->default_template, 'page');
		}
		
		if (isset(template->content) && is_callable(template->content)) {
			(template->content)();
		} else if (file_exists(template->file)) {
			include_once(template->file);
		} else {
			trigger_error('There was no content in the template to render.', E_USER_ERROR);
		}
		
		hooks->after_render_page->execute();
	} else {
		trigger_error('A page render was attempted before a page was selected. This can happen when a non-existent resource is requested and no 404 page is available.', E_USER_ERROR);
	}
};

function render_content() {
	if (defined('Rugosa\page') && page instanceof Page) {
		hooks->before_render_content->execute();
		if (!isset(page->content)) {
			include_once(page->file);
		} elseif (page->content instanceof \Closure) {
			(page->content)();
		} elseif (is_string(page->content)) {
			echo page->content;
		}
		hooks->after_render_content->execute();
		return true;
	} else {
		return false;
	}
};

function use_default_styles() {
	hook('head_tag', "<link rel='stylesheet' type='text/css' href='" . __WEBROOT__ . "/Rugosa/assets/css/rugosa.css'>");
}

function init() {
	session_start();
	ob_start();

	load_sites();
	select_site_from_host('default');

	load_plugins();
	hooks->preload->execute();

	load_themes();
	load_pages();

	select_page_from_url('404');
	select_theme(page->theme, site->theme, 'default');

	use_default_styles();
	render_page();

	hooks->postload->execute();
	ob_end_flush();

	exit();
};

function head_tag() {
	echo "<head>";
	hooks->head_tag->execute();
	echo "</head>";
}

function title_tag() {
	if (site->title || page->title) {
		$titleString = (page->title ?? '') . ((page->title && site->title) ? ' - ' : '') . (site->title ?? '');
		echo "<title>$titleString</title>";
	}
};

/*

Old Helpers

$r->logo =
function() use ($r) {
?><?=$r->site->title?><?php
};

$r->title_tag = 
function() use ($r) {
	if ($r?->site?->title || $r?->page?->title) {
		$titleString = ($r?->page?->title ?? '') . (($r?->page?->title && $r?->site?->title) ? ' - ' : '') . ($r?->site?->title ?? '');
		echo "<title>$titleString</title>";
	}
};

*/

?>