<?php 

// set some defaults
if(!isset($sort)) $sort = 'filename';
if(!isset($direction)) $direction = 'desc';

// get the list of files for this page
$files = $page->files();

// filter the list of files by extension
if(isset($extension)) $files = $files->findByExtension($extension);

// sort the list
$files = $files->sortBy($sort, $direction);

?>
<ul class="filelist">
  <?php foreach($files as $file): ?>
  <li><a href="<?php echo $file->url() ?>"><?php echo html($file->filename()) ?></a></li>    
  <?php endforeach ?>
</ul>