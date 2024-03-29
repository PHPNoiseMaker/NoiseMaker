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

App::uses('Inflect', 'Utility');
App::import('Config', 'Core');
App::import('Config', 'Config');
App::import('base', 'Core');
App::import('ExceptionHandler', 'Errors');
App::import('Exceptions', 'Errors');


set_exception_handler(array("ExceptionHandler", "handleException"));
set_error_handler(array("ExceptionHandler", "handleError"));
spl_autoload_register(array('App', 'autoLoad'));