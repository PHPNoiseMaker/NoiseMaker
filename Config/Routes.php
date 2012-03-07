<?php

$this->router->addRule('/', array(
	'controller' => 'Pages',
	'action' => 'index'
));

$this->router->addRule('/contact', array(
	'controller' => 'Pages',
	'action' => 'display',
	'contact'
));