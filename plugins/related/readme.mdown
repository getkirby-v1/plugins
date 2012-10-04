# Related Pages

## What is it?

This plugin makes it possible to link related pages very easily by their URI in content files. 

## Installation 

Put the related.php file in your site/plugins folder. 

## How to use it?

### In your content files

    Title: My title
    ----
    Text: My text
    ----
    Related: 

    - projects/project-1
    - blog/my-article
   

### In your templates
     
    <?php foreach related($page->related() as $related): ?>
    <a href="<?php echo $related->url() ?>"><?php echo html($related->title()) ?></a>
    <?php endforeach ?>


## Result

The result of the related() function will be a full set of `$pages`, which you can apply the same methods to as you would to a normal set of `$pages`. This makes it possible to do stuff like: 

	$related = related($page->related());
	
	// show the url of the first related page
	echo $related->first()->url();
	
	// show the title of the last related page	
	echo $related->last()->title();
	
	// limit the number of related items
	foreach($related->limit(5) as $related) {
 		// do something 
	}
		
	// etc. 

For each `$page` in the set of related `$pages` you have full access to all the custom fields and methods of the related `$page`


## Author
Bastian Allgeier
<http://getkirby.com>