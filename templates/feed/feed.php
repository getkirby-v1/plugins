<?php 

// get any list of items    
// in this case we get all visible children of the blog section, 
// flip them to get them in reverse order and make sure we only get the last 10
$items = $pages->find('blog')->children()->visible()->flip()->limit(0,10);

// this is how you embed the feed snippet with some options    
snippet('feed', array(
  'link'  => url('blog'),
  'items' => $items,
  'descriptionField'  => 'text', 
  'descriptionLength' => 300
));

?>