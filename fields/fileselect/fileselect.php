<?php

global $site;

$page    = $site->pages()->active();
$files   = $page->files();
$options = array();

if(isset($empty)) {
  $options[0] = '- ' . $empty . ' -';
}

$filetype  = (!isset($filetype))  ? false : (array)$filetype;
$extension = (!isset($extension)) ? false : (array)$extension;

foreach($files as $file) {

  if($file->type() == 'content') continue;
  if($filetype  && !in_array($file->type(), $filetype)) continue;
  if($extension && !in_array($file->extension(), $extension)) continue;
  
  $options[$file->filename()] = $file->filename();
        
}

?>
<select name="<?php echo $name ?>">
  <?php foreach($options AS $key => $text): ?>
  <option<?php if($key == $value) echo ' selected="selected"' ?> value="<?php echo html($key) ?>"><?php echo html($text) ?></option>
  <?php endforeach ?>
</select>