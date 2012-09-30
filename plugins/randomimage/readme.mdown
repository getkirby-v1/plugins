# Random Image

Returns a random image url from a given directory
Can be used to display random header images for example.

## Installation
Put the `randomimage.php` file in your `site/plugins` folder. If this folder doesn't exist yet, create it.

## Example usage

	<header style="background-image: url(<?php echo randomimage('assets/images/header') ?>)">
	  <!-- the html for your header -->
	</header>

## Notes

The directory must be relative to the root of the site, otherwise URLs will be broken

If you are working with images in a content directory you can simplify this by doing: 

	<?php echo $page->images()->shuffle()->first()->url() ?>
	
## Author
Bastian Allgeier <http://getkirby.com>
