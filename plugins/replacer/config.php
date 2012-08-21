<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/*

---------------------------------------
Replacer plugin
---------------------------------------

With c::set('replacer.autouse') you can change the behavior of the global replace function of the Replacer plugin.
false means that global replacements are diabled and can be used with the functions replacer::on() and re::off() to apply the placeholders only on specific parts of the page.
By default (true), all placeholders defined in site/replacer/*.php marked as "usage: global" will be used for the whole output.

With c::set('replacer.regex') you can change if you always want to use Regexes with preg_replace ('regex'), String replacement with str_replace ('string') or if it should be auto-detected ('auto').

See the replacer.mdown for more details.

*/ 

c::set('replacer.autouse', true);
c::set('replacer.regex', 'auto');

?>