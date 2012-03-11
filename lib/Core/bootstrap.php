<?php

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('APP_DIR')) {
	define('APP_DIR', basename(dirname(dirname(__FILE__))));
}
if (!defined('ROOT')) {
	define('ROOT', dirname(dirname(dirname(__FILE__))));
}

require_once('lib/Core/App.php');

App::import('Inflect', 'Utility');
App::import('base', 'Core');
App::import('ExceptionHandler', 'Core');


set_exception_handler(array("ExceptionHandler", "handleException"));
set_error_handler(array("ExceptionHandler", "handleError"));