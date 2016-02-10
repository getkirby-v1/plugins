<?php
//session_start();


/**
  * simple math captcha by Christoph Bach <info@christoph-bach.net> 
 **/
// Generating random numbers for captcha
function captcha() {
	$a = rand(0,9);
	$b = rand(0,9);

	$c = rand(0,1);

	if ($c) {
		$result = $a + $b;
		$question = $a." plus ".$b;
	} else {
		if ($a > $b) {
			$result = $a - $b;
			$question = $a." minus ".$b;
		} else {
			$result = $b - $a;
			$question = $b." minus ".$a;
		}
	}

	$_SESSION['capres'] = $result;

	return $question;
	//return str::encode($question);
}
?>
