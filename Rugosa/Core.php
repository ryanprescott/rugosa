<?php namespace Rugosa;

function panic($errno = null, $errstr = null, $errfile = null, $errline = null) {
while (ob_get_level()) { ob_end_clean(); }
http_response_code('500');
?>
<html>
<head>
	<title>Rugosa</title>
</head>
<body style="background: #500;">
	<style>*{font-family:sans-serif}dialog{top:25%;background:#fff;padding:1rem;border-radius:6px;border:none;box-shadow:2px 2px 3px #000;max-width:800px;}</style>
	<dialog open>
		<h2><span class="rugosa"></span> Rugosa</h2>
		<p><strong>A serious error occurred in <?=$errfile ? "file '" . $errfile . "'" . ($errline ? " on line " . $errline : ""): "your Rugosa site"?>:</strong><br><?=$errstr ?? "The error message could not be displayed"?></p>
		<p>If you are the administrator of this site, it's possible that your site is misconfigured or a plugin is causing this error. Please check the error log to find out more information about what happened.</p>
	</dialog>
</body>
</html>
<?php
die();
}

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
	$rewritten_path = __DIR__ . '/../'. str_replace('\\', '/', $className) . '.php';
	@require_once($rewritten_path);
});

if (PHP_VERSION[0]<8) {
	trigger_error("Rugosa is not compatible with PHP versions before 8.0. Please install the latest version of PHP.", E_USER_ERROR);
}

define('RUGOSA_VERSION', new Version(1, 23, 2, 0));

define('__DOCROOT__', $_SERVER['DOCUMENT_ROOT']);
define('__RELROOT__', Path::diff(getcwd(), __DOCROOT__));
define('__WEBRELROOT__', Path::combine('//',$_SERVER['HTTP_HOST'],__RELROOT__));
define('__WEBROOT__', Path::combine('//',$_SERVER['HTTP_HOST']));

/*** Hooks ***/
$available_hooks = [
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

$hooks = new Collection();

foreach ($available_hooks as $hook) {
	$hooks->set($hook, new Hook());
}

function hook(string $hook, mixed $obj = null) {
	global $hooks;
	if ($hooks->get($hook)) {
		if ($obj === null) {
			return $hooks->{$hook}->execute();
		} else {
			return $hooks->{$hook}->add($obj);
		}
	}
	return false;
};

function load_site(string $path) {
	global $sites;
	if (!isset($sites)) {
		$sites = new Collection;
	}
	if (is_dir($path)) {
		$def = Path::combine($path, "site.php");
		if (file_exists($def)) {
			$block = Metadata::from_php_file($def);
			if (is_array($block)) {

				$site = new Site($block);
				
				if ($sites->add($site)) {
					return true;
				} else {
					trigger_error("load_site: Site '{$site->name}' at '{$path}' could not be loaded either because it has no name, or it is a duplicate of an already loaded theme.");
				}
			} else {
				trigger_error("load_site: Site could not be loaded. File '{$def}' did not contain a valid site declaration.");
			}
		} else {
			trigger_error("load_site: Site could not be loaded. File '{$def}' does not exist.");
		}
	} else {
		trigger_error("load_site: Site could not be loaded. Supplied path '$path' does not exist or was not a directory.");
	}
	return false;
};

function load_sites() {
	$sitesDir = Path::combine(getcwd(), 'sites');
	if(is_dir($sitesDir)) {
		$siteDirs = array_diff(scandir($sitesDir), [".", ".."]);
		foreach($siteDirs as $siteDir) {
			if (substr($siteDir, 0, 1) !== "!") {
				$path = Path::combine($sitesDir, $siteDir);
				load_site($path);
			}
		}
	} else {
		trigger_error('load_sites: Sites directory does not exist or is not a directory.');
		return false;
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
	global $themes;

	if (!isset($themes)) {
		$themes = new Collection;
	}

	if (is_dir($path)) {
		$def = Path::combine($path, "theme.php");
		if (file_exists($def)) {
			$block = Metadata::from_php_file($def);
			if (is_array($block)) {

				$block["templates"] = import_templates($path);
				$theme = new Theme($block);
				
				if ($themes->add($theme)) {
					return true;
				} else {
					trigger_error("load_theme: Theme '{$theme->name}' at '{$path}' could not be loaded either because it has no name, or it is a duplicate of an already loaded theme.");
				}
			} else {
				trigger_error("load_theme: Theme could not be loaded. File '{$def}' did not contain a valid theme declaration.");
			}
		} else {
			trigger_error("load_theme: Theme could not be loaded. File '{$def}' does not exist.");
		}
	} else {
		trigger_error("load_theme: Theme could not be loaded. Supplied path '$path' does not exist or was not a directory.");
	}
	return false;
};

function load_themes() {
	global $site;
	$themesDir = Path::combine($site->dir, 'themes');
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
	global $plugin, $plugins;

	if (!isset($plugins)) {
		$plugins = new Collection;
	}

	if (is_dir($path)) {
		$def = Path::combine($path, "plugin.php");
		if (file_exists($def)) {
			$block = Metadata::from_php_file($def);
			if (is_array($block)) {
				$newPlugin = new Plugin($block);
				if ($plugins->add($newPlugin)) {
					$plugin = $newPlugin;
					include_once($def);
					unset($plugin);
					return true;
				} else {
					trigger_error("load_plugin: Plugin '{$plugin->name}' at '{$path}' could not be loaded either because it has no name, or it is a duplicate of an already loaded plugin.");
				}
			} else {
				trigger_error("load_plugin: Plugin could not be loaded. File '{$def}' did not contain a valid plugin declaration.");
			}
		} else {
			trigger_error("load_plugin: Plugin could not be loaded. File '{$def}' does not exist.");
		}
	} else {
		trigger_error("load_plugin: Plugin could not be loaded. Supplied path '$path' does not exist or was not a directory.");
	}
	return false;
};

function load_plugins() {
	global $site;

	$pluginsDir = Path::combine($site->dir, 'plugins');
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
	global $pages;

	if (!isset($pages)) {
		$pages = new Collection;
	}

	if (file_exists($path)) {
		$block = Metadata::from_php_file($path);
		if (is_array($block)) {
			$page = new Page($block);
			return $pages->add($page);
		} else {
			trigger_error("load_page: File '{$path}' did not contain a valid page declaration.");
		}
	} else {
		trigger_error('load_page: Page could not be loaded. The file specified does not exist.');		
	}
	return false;
};

function load_pages() {
	global $site;

	hook("before_load_pages");

	$pagesDir = Path::combine($site->dir, 'pages');

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
	global $sites, $site;

	$args = func_get_args();
	if ($args[0] instanceof Collection) {
		$siteCollection = array_shift($args);
	} elseif ($sites instanceof Collection) {
		$siteCollection = $sites;
	} else {
		trigger_error('The default site collection was not available and no collection was specified.', E_USER_ERROR);
		return false;
	}

	foreach($args as $selector) {
		if ($siteCollection->has($selector)) {
			$site = $siteCollection->get($selector);
			break;
		}
	}

	if ($site) {
		include_once($site->file);
		return true;
	} else {
		trigger_error('No site was found matching the selectors specified: ' . join(', ', $args), E_USER_ERROR);
		return false;
	}
};

function select_page() {
	global $pages, $page;

	$args = func_get_args();
	if ($args[0] instanceof Collection) {
		$pageCollection = array_shift($args);
	} elseif ($pages instanceof Collection) {
		$pageCollection = $pages;
	} else {
		trigger_error('The default page collection was not available and no collection was specified.', E_USER_ERROR);
		return false;
	}

	foreach($args as $selector) {
		if ($pageCollection->has($selector)) {
			$page = $pageCollection->get($selector);
			break;
		}
	}

	if ($page) {
		return true;
	} else {
		trigger_error('No page was found matching the selectors specified: ' . join(', ', $args), E_USER_ERROR);
		return false;
	}
};

function get_page_selector_from_url() {
	global $site;
	error_log(json_encode($site));
	return trim(strtok($_SERVER['REQUEST_URI'], '?'), '/') ?: (isset($site->default_page) ? $site?->default_page : 'home');
};

function select_page_from_url(mixed $default = null) {
	global $page_selector;
	$page_selector = get_page_selector_from_url();
	return select_page($page_selector, $default);
};

function get_site_selector_from_host() {
	return $_SERVER['HTTP_HOST'] ?: 'default';
};

function select_site_from_host(mixed $default = null) {
	global $selector;
	$selector = get_page_selector_from_url();
	return select_site($selector, $default);
};

function select_theme() {
	global $themes, $theme;
	$args = func_get_args();
	if ($args[0] instanceof Collection) {
		$themeCollection = array_shift($args);
	} elseif ($themes instanceof Collection) {
		$themeCollection = $themes;
	} else {
		trigger_error('The default theme collection was not available and no collection was specified.', E_USER_ERROR);
		return false;
	}

	foreach($args as $selector) {
		if ($themeCollection->has($selector)) {
			$theme = $themeCollection->get($selector);
			break;
		}
	}

	if ($theme) {
		return true;
	} else {
		trigger_error('No theme was found matching the selectors specified: ' . join(', ', $args), E_USER_ERROR);
		return false;
	}
};

function select_template() {
	global $theme, $template;

	$args = func_get_args();
	if ($args[0] instanceof Collection) {
		$templateCollection = array_shift($args);
	} elseif ($theme->templates instanceof Collection) {
		$templateCollection = $theme->templates;
	} else {
		trigger_error('The default template collection was not available and no collection was specified.', E_USER_ERROR);
		return false;
	}

	foreach($args as $selector) {
		if ($templateCollection->has($selector)) {
			$template = $templateCollection->get($selector);
			break;
		}
	}

	if ($template) {
		return true;
	} else {
		trigger_error('No template was found matching the selectors specified: ' . join(', ', $args), E_USER_ERROR);
		return false;
	}
};

function render_page() {
	global $page, $hooks, $template, $site, $theme;

	if (isset($page) && $page instanceof Page) {

		$hooks->before_render_page->execute();

		if (!$template instanceof Template) {
			select_template(isset($page->template) && $page->template, $site->template, isset($theme->default_template) && $theme->default_template, 'page');
		}
		
		if (isset($template->content) && is_callable($template->content)) {
			($template->content)();
		} else if (file_exists($template->file)) {
			include_once($template->file);
		} else {
			trigger_error('There was no content in the template to render.', E_USER_ERROR);
		}
		
		$hooks->after_render_page->execute();
	} else {
		trigger_error('A page render was attempted before a page was selected. This can happen when a non-existent resource is requested and no 404 page is available.', E_USER_ERROR);
	}
};

function render_content() {
	global $page, $hooks;
	if ($page) {
		$hooks->before_render_content->execute();
		if (!isset($page->content)) {
			include_once($page->file);
		} elseif ($page->content instanceof \Closure) {
			($page->content)();
		} elseif (is_string($page->content)) {
			echo $page->content;
		}
		$hooks->after_render_content->execute();
		return true;
	} else {
		return false;
	}

};
function use_default_styles() {
	hook('head_tag', "<link rel='stylesheet' type='text/css' href='" . __WEBROOT__ . "/Rugosa/assets/css/rugosa.css'>");
}

function init() {
	global $hooks, $page, $site;

	session_start();
	ob_start();

	load_sites();
	select_site_from_host('default');

	load_plugins();
	$hooks->preload->execute();

	load_themes();
	load_pages();

	select_page_from_url('404');
	select_theme(isset($page->theme) && $page->theme, $site->theme, 'default');

	use_default_styles();
	render_page();

	$hooks->postload->execute();
	ob_end_flush();

	exit();
};

function head_tag() {
	global $hooks;
	echo "<head>";
	$hooks->head_tag->execute();
	echo "</head>";
}
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