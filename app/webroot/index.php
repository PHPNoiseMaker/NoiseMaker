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

set_include_path(ROOT);

require_once 'app/Main.php';