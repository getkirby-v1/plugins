Intergrate Fancybox 2 into Kirby.
==================

Download Fancybox 2

- http://fancyapps.com/fancybox/#license

Drop the source/ folder into site/plugins/fancybox


Copy and Paste this into your site/snippets/header.php before </head>
```html
<!-- Add jQuery library -->
	<?php echo js('http://code.jquery.com/jquery-latest.min.js') ?>

<!-- Add mousewheel plugin (this is optional) -->
	<?php echo js('assets/fancybox/lib/jquery.mousewheel-3.0.6.pack.js') ?>

<!-- Add fancyBox -->
	<?php echo css('assets/fancybox/source/jquery.fancybox.css?v=2.1.5') ?>
	<?php echo js('assets/fancybox/source/jquery.fancybox.pack.js?v=2.1.5') ?>

<!-- Optionally add helpers - button, thumbnail and/or media -->
	<?php echo css('assets/source/helpers/jquery.fancybox-buttons.css?v=1.0.5') ?>
	<?php echo js('assets/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5') ?>
	<?php echo js('assets/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6') ?>

	<?php echo css('assets/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7') ?>
  <?php echo js('assets/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7') ?>

<script type="text/javascript">
<!--
	$(document).ready(function() {
		$("a.fancybox").fancybox();
	});
-->
</script>
```

## Usage
```html
(image: image1.jpg link: image1.jpg title: This is a wonderful Image width: 175 popup: yes class: fancybox)
```

- **image:** link to the image you want to view.
- **link:** link to the image or a high resolution version of the image.
- **title:** this places a "title" description below the fancybox2 attributed image.
- **width:** if you're lazy to not make a thumbnail, resize the image down to a thumbnail size view on the page. (Doesn't affect the size of the fancybox view'd image.)
- **popup:** Best to put yes, if Javascript is disabled by a user - some do?! The image will popup in a new tab. Leaving this one out won't affect the fancybox2 script.
- **class:** Make this 'fancybox' for any images you wish the fancybox2 script applied to.

## Licence
I'm not sure what licence to put. Although Fancybox2 has it's own licence and I recommend all users to read the Fancybox2 licence.

# Author
Cameron Walker - http://www.t94xr.net.nz/
