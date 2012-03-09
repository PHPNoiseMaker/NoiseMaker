<?php
/*
 * Functions which are globally available
 */
 
/**
 * Gets environment variables, or returns FASLE if not found
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

/**
 * wrapper function for htmlspecialchars
 */
function h($string = null) {
	return htmlspecialchars($string);
}