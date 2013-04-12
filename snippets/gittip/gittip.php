<?php
$err_state = error_reporting();
error_reporting(0); // Turn off errors for this script.

if (!$user_id) { // If no user_id was passed...
    echo("<span style=\"color: red;\">No gittip user defined.</span>"); // ...echo error.
} else { // Otherwise
?>

    <iframe style="border: 0; margin: 0; padding: 0;" src="https://www.gittip.com/<?php echo $user_id ?>/widget.html" width="48pt" height="22pt"></iframe>

<?php
};
error_reporting($err_state); // Restore error reporting to original state.
?>