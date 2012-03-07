<?php

$this->router->addRule('/', array(
	'controller' => 'Pages',
	'action' => 'display'
));

if(file_exists(ROOT . DS .'Config/Routes.php')) {
	include_once 'Config/Routes.php';
}