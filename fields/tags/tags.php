<?php

global $site;

$id    = uniqid() . '-' . $name;
$page  = $site->pages()->active();

// define the separating character
$separator = (isset($separator)) ? $separator : ',';

// field to fetch existing tags from
$field = (isset($field)) ? $field : $name;

// field to fetch existing tags from (since by default there was none, but you might want
// to establish a default in your site config via c::set(field.tags.index)
$default_index = trim(c::get('fields.tags.index'));
$index = (isset($index)) ? $index : ( ( !empty($default_index) ) ? $default_index : '' );

// lowercase all tags
$lower = (isset($lower)) ? $lower : false;

// use passed data if available or try to fetch data
if(!isset($data) || !is_array($data)) {

	$data  = array();
	$store = array();

  switch($index) {
	// Need to establish a default otherwise PHP Error 'Notices' can occur (if not suppressed)
	default:
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