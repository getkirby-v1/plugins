<?php

// stop without videos
if(empty($videos)) return;

// set some defaults
if(!isset($width))    $width    = 400;
if(!isset($height))   $height   = 300;
if(!isset($preload))  $preload  = true;
if(!isset($controls)) $controls = true;

// build the html atts for the video element
$preload  = ($preload)  ? ' preload="preload"'   : '';
$controls = ($controls) ? ' controls="controls"' : '';
$poster = ($thumb) ? ' poster="'. $thumb->url() .'"' : '';

?>
<video width="<?php echo $width ?>" height="<?php echo $height ?>"<?php echo $preload . $controls . $poster ?>>
  <?php foreach($videos as $video): ?>
  <source src="<?php echo $video->url() ?>" type="<?php echo $video->mime() ?>" />
  <?php endforeach ?>
  <?php if(isset($thumb)): ?>
  <img src="<?php echo $thumb->url() ?>" alt="<?php echo $thumb->title() ?>" />
  <?php endif ?>
</video>