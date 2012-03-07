<?php

$this->router->addRule('', array(
	'controller' => 'Pages',
	'action' => 'display'
));

$this->router->addRule('/contact', array(
	'controller' => 'Pages',
	'action' => 'index',
	'test'
));