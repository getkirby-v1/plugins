<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/*

---------------------------------------
Assety plugin
---------------------------------------

With this options you can disable compressing and caching of specific file types.

Enabling the html option will compress the whole (normal) output, even if it's no HTML, so don't
use it if you build a Kirby application echoing JSON or XML for example.

*/

c::set('assety.css', true);
c::set('assety.js', true);
c::set('assety.html', false);

/*

To use compressing of CSS and JS files and compiling of LESS/SASS files, it's required to activate caching.
Please make sure it is activated in the config file
and your site/cache-folder is writable!

*/

?>