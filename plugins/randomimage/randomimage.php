<?php

/*
 * Returns a random image from a given directory
 * Can be used to display random header images for example.
 * 
 * Usage: 
 * 
 * <code>
 * <header style="background-image: url(<?php echo randomimage('assets/images/header') ?>)"></header>
 * </code>
 * 
 * The directory must be relative to the root of the site. 
 * Otherwise URLs will be broken
 * 
 * @param  string $dir the relative path to the directory
 * @return string the full url to the image
 */
function randomimage($dir) {
  $files = dir::read(c::get('root') . '/' . $dir); 
  shuffle($files);
  return url($dir . '/' . a::first($files));
}

?>