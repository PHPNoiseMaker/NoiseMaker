<?php
require_once('lib/bootstrap.php');
require_once('lib/router.php');

try {
	new Router();
} catch(Exception $e) {
	new Router('Errors', 'index');
}
