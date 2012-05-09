<?php

$instances = g::get('kflattr.instances');
g::set('kflattr.instances', $instances+1);

// set the defaults
if(!isset($user_id))      die('Please pass the flattr userid');
if(!isset($url))          $url = $page->url();
if(!isset($title))        $title = $page->title();
if(!isset($description))  $description = null;
if(!isset($category))     $category = null;
if(!isset($language))     $language = null;
if(!isset($tags))         $tags = null;
if(!isset($button))       $button = null;
if(!isset($popout))       $popout = null;
if(!isset($hidden))       $hidden = null;
if(!isset($html5))        $html5 = false;

if(!$instances) :

// javascript output (once)
?>
<script type="text/javascript">
  /* <![CDATA[ */
  (function() {
    var s = document.createElement('script');
    var t = document.getElementsByTagName('script')[0];

    s.type = 'text/javascript';
    s.async = true;
    s.src = 'http://api.flattr.com/js/0.6/load.js?mode=auto';

    t.parentNode.insertBefore(s, t);
  })();
  /* ]]> */
</script>
<? endif;

// html5 output
if($html5 != false) :
  $atts = ' data-flattr-uid="' . $user_id . '"';
  if(!is_null($description))  $atts .= ' data-flattr-description="' . $description . '"';
  if(!is_null($category))   $atts .= ' data-flattr-category="' . $category . '"';
  if(!is_null($language))   $atts .= ' data-flattr-language="' . $language . '"';
  if(!is_null($tags))     $atts .= ' data-flattr-tags="' . $tags . '"';
  if(!is_null($button))   $atts .= ' data-flattr-button="compact"';
  if(!is_null($popout))   $atts .= ' data-flattr-popout="0"';
  if(!is_null($hidden))   $atts .= ' data-flattr-hidden="1"';
?>
<a class="FlattrButton" style="display:none;" title="<? echo $title ?>"<? echo $atts; ?> href="<? echo $url ?>">Flattr this!</a>
<?php

// _oldschool_ html output
else : 
  $rel = 'flattr;uid:' . $user_id . ';';
  if(!is_null($description))  $rel .= 'description:' . $description . ';';
  if(!is_null($category))   $rel .= 'category:' . $category . ';';
  if(!is_null($language))   $rel .= 'language:' . $language . ';';
  if(!is_null($tags))     $rel .= 'tags:' . $tags . ';';
  if(!is_null($button))   $rel .= 'button:compact;';
  if(!is_null($popout))   $rel .= 'popout:0;';
  if(!is_null($hidden))   $rel .= 'hidden:1;';
?>
<a class="FlattrButton" style="display:none;" title="<? echo $title ?>" rel="<? echo $rel ?>" href="<? echo $url ?>">Flattr this!</a>
<? endif;

// noscript output
$nosurl = 'https://flattr.com/submit/auto?user_id=' . $user_id . '&url=' . urlencode($url);
if(!is_null($title))      $nosurl .= '&title=' . urlencode($title);
if(!is_null($description))    $nosurl .= '&description=' . urlencode($description);
if(!is_null($category))     $nosurl .= '&category=' . urlencode($category);
if(!is_null($language))     $nosurl .= '&language=' . urlencode($language);
if(!is_null($tags))       $nosurl .= '&tags=' . urlencode(implode(',', $tags));
if(!is_null($hidden))     $nosurl .= '&hidden=1';
?>
<noscript><a href="<? echo $nosurl ?>" title="Flattr this!"><img src="https://api.flattr.com/button/flattr-badge-large.png" alt="Flattr Button" /></a></noscript>