<?php
/*
 * Functions which are globally available
 */
 

function env($string = null) {
	$return = null;
	if($string !== null) {
		if(isset($_SERVER[$string])) {
			$return = $_SERVER[$string];
		} elseif (isset($_ENV[$string])) {
			$return = $_ENV[$string];
		}
	}
	if($return !== null) {
		return $return;
	}
	return false;
	
}