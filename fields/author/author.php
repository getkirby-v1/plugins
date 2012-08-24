<?php

global $site;

if(!$value) $value = $site->user->username;

?>
<input type="text" class="input" name="<?php echo html($name) ?>" value="<?php echo html($value) ?>" />
