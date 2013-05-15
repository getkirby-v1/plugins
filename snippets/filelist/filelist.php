<?php 

// set some defaults
if(!isset($sort)) $sort = 'filename';
if(!isset($direction)) $direction = 'desc';
if(!isset($exclude)) $exclude = array('md','mdown','txt');

// get the list of files for this page
$files = $page->files();

// filter the list of files by extension
if(isset($extension)) $files = $files->findByExtension($extension);

// sort the list
$files = $files->sortBy($sort, $direction);

// when excluding files, the UL will still be returned if there are no files to show
foreach($files as $file):
  if(in_array($file->extension(),$exclude)):
    unset($files->_[$file->filename]);
  endif;
endforeach;

if(count($files->_)>0): 
?>

<ul class="filelist">
  <?php foreach($files as $file): ?>
  <li><a href="<?php echo $file->url() ?>"><?php echo html($file->filename()) ?></a></li>    
  <?php endforeach; ?>
</ul>

<?php endif; ?>
