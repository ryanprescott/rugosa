<?php namespace Rugosa;

function panic($errno = null, $errstr = null, $errfile = null, $errline = null) {
if (ob_get_level()) { ob_end_clean(); }
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

// ini_set('display_errors', 0);

spl_autoload_register(function($className) {
	$rewritten_path = str_replace('\\', '/', __DIR__ . '/../'. $className) . '.php';
	@require_once($rewritten_path);
});

if (PHP_VERSION[0]<8) {
	trigger_error("Rugosa is not compatible with PHP versions before 8.0. Please install the latest version of PHP.", E_USER_ERROR);
}

class Core {
	public function __call($name, $arguments) {
		if (is_callable($this->{$name})) {
			return ($this->{$name})(...$arguments);
		} else {
			echo $this->{$name};
		}
	}
};

$r = new Core;
$r->version = new Version(0, 21, 9, 3);

define('__DOCROOT__', $_SERVER['DOCUMENT_ROOT']);
define('__RELROOT__', Path::diff(getcwd(), __DOCROOT__));
define('__WEBRELROOT__', Path::combine('//',$_SERVER['HTTP_HOST'],__RELROOT__));
define('__WEBROOT__', Path::combine('//',$_SERVER['HTTP_HOST']));

$r->backtrace = function() use ($r) {
	$backtrace = debug_backtrace();
	$str = '';
	foreach($item as $backtrace) {
		$str .= implode($item, ',') . '\n';
	}
};

/*** Hooks ***/
$r->available_hooks = [
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

$r->hooks = new Core();
foreach ($r->available_hooks as $hook) {
	$r->hooks->{$hook} = new Hook;
}

$r->hook =
function(string $hook, $obj = null) use ($r) {
	if ($r->hooks->{$hook}) {
		if ($obj === null) {
			return $r->hooks->{$hook}();
		} else {
			return $r->hooks->{$hook}->add($obj);
		}
	}
	return false;
};

$r->load_site = 
function() use ($r) {
	if (isset($r->site) && $r->site instanceof Site) {
		trigger_error("load_site: A site has already been loaded.");
	} else {
		if (is_dir("site")) {
			if (file_exists("site/site.php")) {
				$block = Metadata::from_php_file("site/site.php");
				if (is_array($block)) {
					$r->hooks->before_load_site();
					$site = new Site($block);
					$r->site = $site;
					$r->hooks->after_load_site();
					return true;
				} else {
					trigger_error("load_site: Site could not be loaded. File 'site.php' did not contain a valid site declaration.", E_USER_ERROR);
				}
			} else {
				trigger_error("load_site: site.php does not exist.", E_USER_ERROR);
			}
		} else {
			trigger_error("load_site: The site directory does not exist.", E_USER_ERROR);
		}
	}
	return false;
};

$r->load_theme =
function($path) use ($r) {
	if (!isset($r->themes)) {
		$r->themes = new Collection;
	}
	if (is_dir($path)) {
		$def = Path::combine($path, "theme.php");
		if (file_exists($def)) {
			$block = Metadata::from_php_file($def);
			if (is_array($block)) {
				$theme = new Theme($block);
				if ($r->themes->add($theme)) {
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

$r->load_themes =
function() use ($r) {
	if(is_dir("themes")) {
		$themeDirs = array_diff(scandir("themes"), [".", ".."]);
		foreach($themeDirs as $themeDir) {
			if (substr($themeDir, 0, 1) !== "!") {
				$path = Path::combine("themes", $themeDir);
				$r->load_theme($path);
			}
		}
	} else {
		trigger_error('load_themes: Themes directory does not exist or is not a directory.');
		return false;
	}
};

$r->load_plugin =
function($path) use ($r) {
	if (!isset($r->plugins)) {
		$r->plugins = new Collection;
	}
	if (is_dir($path)) {
		$def = Path::combine($path, "plugin.php");
		if (file_exists($def)) {
			$block = Metadata::from_php_file($def);
			if (is_array($block)) {
				$plugin = new Plugin($block);
				if ($r->plugins->add($plugin)) {
					$r->plugin = $plugin;
					include_once($def);
					unset($r->plugin);
					return true;
				} else {
					trigger_error("load_plugin: Plugin '{$r->plugin->name}' at '{$path}' could not be loaded either because it has no name, or it is a duplicate of an already loaded plugin.");
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

$r->load_plugins =
function() use ($r) {
	if(is_dir("plugins")) {
		$pluginDirs = array_diff(scandir("plugins"), [".", ".."]);
		foreach($pluginDirs as $pluginDir) {
			if (substr($pluginDir, 0, 1) !== "!") {
				$path = Path::combine("plugins", $pluginDir);
				$r->load_plugin($path);
			}
		}
	} else {
		trigger_error('load_plugins: Plugins directory does not exist or is not a directory. If you do not expect to use');
		return false;
	}
};

$r->load_page =
function($path) use ($r) {
	if (!isset($r->pages)) {
		$r->pages = new Collection;
	}
	if (file_exists($path)) {
		$block = Metadata::from_php_file($path);
		if (is_array($block)) {
			$page = new Page($block);
			return $r->pages->add($page);
		} else {
			trigger_error("load_page: File '{$path}' did not contain a valid page declaration.");
		}
	} else {
		trigger_error('load_page: Page could not be loaded. The file specified does not exist.');		
	}
	return false;
};

$r->load_pages =
function() use ($r) {
	$r->hook("before_load_pages");
	if(is_dir('pages')) {
		$pageFiles = array_diff(scandir('pages'), [".", ".."]);
		if (count($pageFiles) > 0) {
			foreach($pageFiles as $pageFile) {
				if (substr($pageFile, 0, 1) !== '!') {
					$path = Path::combine('pages', $pageFile);
					$r->load_page($path);
				}
			}
			$r->hook("after_load_pages");
			return true;
		}
	}
	trigger_error('load_pages: Your site has no pages. Please create some pages before attempting to initialize Rugosa.', E_USER_ERROR);
};

$r->select_page =
function() use ($r) {
	$args = func_get_args();
	if ($args[0] instanceof Collection) {
		$pageCollection = array_shift($args);
	} elseif ($r?->pages instanceof Collection) {
		$pageCollection = $r->pages;
	} else {
		trigger_error('The default page collection was not available and no collection was specified.', E_USER_ERROR);
		return false;
	}

	foreach($args as $selector) {
		if (array_key_exists($selector, $pageCollection->items)) {
			$r->page = $pageCollection->items[$selector];
			break;
		}
	}

	if ($r->page) {
		return true;
	} else {
		trigger_error('No page was found matching the selectors specified: ' . join(', ', $args), E_USER_ERROR);
		return false;
	}
};

$r->select_page_from_url =
function($default = null) use ($r) {
	$r->selector = trim(strtok($_SERVER['REQUEST_URI'], '?'), '/') ?: $r->site->default_page ?: 'home';
	return $r->select_page($r->selector, $default);
};

$r->select_theme =
function() use ($r) {
	$args = func_get_args();
	if ($args[0] instanceof Collection) {
		$themeCollection = array_shift($args);
	} elseif ($r?->themes instanceof Collection) {
		$themeCollection = $r->themes;
	} else {
		trigger_error('The default theme collection was not available and no collection was specified.', E_USER_ERROR);
		return false;
	}

	foreach($args as $selector) {
		if (array_key_exists($selector, $themeCollection->items)) {
			$r->theme = $themeCollection->items[$selector];
			break;
		}
	}

	if ($r->theme) {
		return true;
	} else {
		trigger_error('No theme was found matching the selectors specified: ' . join(', ', $args), E_USER_ERROR);
		return false;
	}
};

$r->render_page = 
function() use ($r) {
	if (isset($r->page) && $r->page instanceof Page) {

		$r->hooks->before_render_page();

		$template = $r->page->template ?? $r->site->template ?? $r->theme->default_template ?? 'page';
		$path = Path::combine($r->theme->dir, $template . '.php');
		if (file_exists($path)) {
			include_once($path);
		} else {
			trigger_error("render_page: File '{$path}' was not found.", E_USER_ERROR);
		}
		
		$r->hooks->after_render_page();
	} else {
		trigger_error('A page render was attempted before a page was selected. This can happen when a non-existent resource is requested and no 404 page is available.', E_USER_ERROR);
	}
};


$r->render_content = 
function() use ($r) {
	if ($r->page) {
		$r->hooks->before_render_content();
		if (!isset($r->page->content)) {
			include_once($r->page->file);
		} elseif ($r->page->content instanceof \Closure) {
			($r->page->content)();
		} elseif (is_string($r->page->content)) {
			echo $r->page->content;
		}
		$r->hooks->after_render_content();
		return true;
	} else {
		return false;
	}

};

$r->init =
function() use ($r) {
	session_start();
	ob_start();
	$r->load_plugins();
	$r->hooks->preload();
	$r->load_site();
	$r->load_pages();
	$r->load_themes();
	$r->select_page_from_url('404');
	$r->select_theme($r->page->theme, $r->site->theme, 'default');
	$r->use_default_styles();
	$r->render_page();
	$r->hooks->postload();
	ob_end_flush();
	exit();
};

/* Stylistic functions */

$r->logo =
function() use ($r) {
?><h3><?=$r->site->title?></h3><?php
};

$r->title_tag = 
function() use ($r) {
	if ($r?->site?->title || $r?->page?->title) {
		$titleString = ($r?->page?->title ?? '') . (($r?->page?->title && $r?->site?->title) ? ' - ' : '') . ($r?->site?->title ?? '');
		echo "<title>$titleString</title>";
	}
};

$r->head_tag =
function() use ($r) {
	echo "<head>";
	$r->hooks->head_tag();
	echo "</head>";
};

$r->use_default_styles =
function() use ($r) {
	$r->hook('head_tag', "<link rel='stylesheet' type='text/css' href='" . __WEBROOT__ . "/Rugosa/assets/css/rugosa.css'>");
}
?>