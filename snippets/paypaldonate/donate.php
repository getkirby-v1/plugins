<?

/*
 * This snippet allows you to embed a PayPal Donate button
 * in your Kirby-based website. This snippet gives you the
 * ability to choose from the various types of buttons
 * offered by PayPal without having to modify your code
 * each and every time.
 *
 * Usage:
 * <?php snippet('disqus', array('type' => 3, 'button_id' => 'BUTTON_ID')) ?>
 *
 * Type 1 - Small button
 * Type 2 - Large button
 * Type 3 - Large button with credit card icon (default)
 * 
 * written by @cedwardsmedia  -  www.cedwardsmedia.com
*/

if ($type == 1) {
$src = "btn_donate_SM.gif";
} elseif ($type == 2) {
$src = "btn_donate_LG.gif";
} else {
$src = "btn_donateCC_LG.gif";
}
if(isset($button_id) && $button_id != "")  {
?>
<div align="center">
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="<? echo $button_id ?>">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/<? echo $src ?>" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
</div>
<?
} else {
echo "<strong>Notice:</strong> No &#36;button_id specified.";
}
?>
