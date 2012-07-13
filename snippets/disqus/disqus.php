<?php

// set the defaults
if(!isset($disqus_shortname))  die('Please pass the disqus shortname');
if(!isset($disqus_title))      $disqus_title = $page->title();
if(!isset($disqus_developer))  $disqus_developer = false;
if(!isset($disqus_identifier)) $disqus_identifier = $page->uri();
if(!isset($disqus_url))        $disqus_url = thisURL();

// sanatize the developer setting
$disqus_developer = ($disqus_developer) ? 'true' : 'false';

?>
<div id="disqus_thread"></div>
<script type="text/javascript">
  var disqus_shortname  = '<?php echo $disqus_shortname ?>'; // required: replace example with your forum shortname
  var disqus_title      = '<?php echo html($disqus_title) ?>';
  var disqus_developer  = '<?php echo $disqus_developer ?>'; // developer mode
  var disqus_identifier = '<?php echo $disqus_identifier ?>';
  var disqus_url        = '<?php echo $disqus_url ?>';

  (function() {
    var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
    dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
  })();
</script>
<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
<a href="http://disqus.com" class="dsq-brlink">blog comments powered by <span class="logo-disqus">Disqus</span></a>