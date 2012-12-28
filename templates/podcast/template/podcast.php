<?php 

// Get items
$items = $pages->find('podcast')->children()->visible()->flip();

// Include podcast snippet
snippet('podcast', array(
    'items' => $items,
    'categories' => array(
        'Technology' => array(
            'Gadgets'
        ), 
        'TV &amp; 
        Film'
    )
));

?>
