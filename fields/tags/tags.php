<?php

global $site;

$id    = uniqid() . '-' . $name;
$page  = $site->pages()->active();

// define the separating character
$separator = (isset($separator)) ? $separator : ',';

// field to fetch existing tags from
$field = (isset($field)) ? $field : $name;

// lowercase all tags
$lower = (isset($lower)) ? $lower : false;

// use passed data if available or try to fetch data
if(!isset($data) || !is_array($data)) {

  $data  = array();
  $store = array();
  
  switch($index) {
    case 'template':
      foreach($site->pages()->index() as $p) {
        if($p->template() == $page->template()) $store[] = $p;
      }
      break;
    case 'all':
      $store = $site->pages()->index();
      break;
    case 'siblings':
      $store = $page->siblings();
      break;
  }
  
  // get all tags
  foreach($store as $s) {
    $data = array_merge($data, str::split($s->{$field}, $separator));
  }

}

// make sure we get a nice array
$data = array_values(array_unique($data));
sort($data);

?>
<input type="text" id="<?php echo $id ?>" class="input" name="<?php echo html($name) ?>" value="<?php echo html($value) ?>" />

<script type="text/javascript">
$(function() {
  $('#<?php echo $id ?>').tagbox({
    url : <?php echo json_encode((array)$data) ?>, 
    lowercase : '<?php echo $lower ?>',
    separator : '<?php echo $separator ?>'
  });
});
</script>