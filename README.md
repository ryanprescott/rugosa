# Rugosa Web Framework
Rugosa is a small object-oriented web framework written in PHP that allows websites to be created and published quickly and easily. Rugosa is fully modular, and plugins and themes can utilize hooks to integrate their functionality into the core.

Rugosa does not by default include any blog functionality or even a WYSIWYG editor. Features can be added or removed on a case-by-case basis. The simplest web pages can be popped up with little to no overhead, and more complex sites can utilize plugins and themes to live up to the standards of the modern web.

All objects that are not a part of the core (i.e., sites, pages, plugins and themes) are loaded from flat files. Each object is instantiated with a small metadata block, and the rest of the code is written directly below it. Here is an example of a page:

```php
<?php
/*--
name=home
title=Home
description=My home page.
--*/
?>
<h2>Hello world!</h2>
<p>Thank you for visiting my site!</p>
```

If this file were to be placed in the 'pages' directory of your Rugosa instance, you would already have your first home page. It's really that easy.

It's possible to expand and modify this framework to include more robust features such as database drivers, blog functionality, search engines, the list goes on. The interesting part is being able to do it without modifying the original source whatsoever. That's what makes Rugosa fun.

Rugosa is designed with modern technologies in mind and therefore is not compatible with any PHP version before 8.2. The default theme is not compatible with Internet Explorer 8 or below.-
