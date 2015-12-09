<?php

// set the defaults
if(!isset($disqus_shortname))  die('Please pass the disqus shortname');
if(!isset($disqus_title))      $disqus_title = $page->title();
if(!isset($disqus_developer))  $disqus_developer = false;
if(!isset($disqus_identifier)) $disqus_identifier = $page->uri();
if(!isset($disqus_url))        $disqus_url = thisURL();

$disqus_title     = addcslashes($disqus_title, "'");
$disqus_developer = ($disqus_developer) ? 'true' : 'false';

?>
<div id="disqus_thread" data-disqusshortname="<?php echo $disqus_shortname ?>" data-disqustitle="<?php echo html($disqus_title) ?>" data-disqusdeveloper="<?php echo $disqus_developer ?>" data-disqusidentifier="<?php echo $disqus_identifier ?>" data-disqusurl="<?php echo $disqus_url ?>"></div>
<script type="text/javascript">
  var disqus = document.getElementById('disqus_thread');
	var disqus_shortname = disqus.dataset.disqusshortname;
	var disqus_title = disqus.dataset.disqustitle;
	var disqus_developer = disqus.dataset.disqusdeveloper;
	var disqus_identifier = disqus.dataset.disqusidentifier;
	var disqus_url = disqus.dataset.disqusurl;

  (function() {
    var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
    dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
  })();
</script>
<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
<a href="http://disqus.com" class="dsq-brlink">blog comments powered by <span class="logo-disqus">Disqus</span></a>
