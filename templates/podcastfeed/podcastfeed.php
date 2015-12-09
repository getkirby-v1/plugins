<?php 

// get the main podcast page with all info about the podcast
$podcast = $pages->find('podcast');

// get any list of items    
// in this case we get all visible children of the podcast section, 
// flip them to get them in reverse order and make sure we only get the last 10
$items = $podcast->children()->visible()->flip()->limit(10);

// this is how you embed the feed snippet with some options    
snippet('podcastfeed', array(
  'podcast' => $podcast,
  'items'   => $items,
));

?>