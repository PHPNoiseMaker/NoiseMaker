<?php
require_once('lib/Core/bootstrap.php');
App::uses('Dispatcher', 'Router');
App::uses('Request', 'Network');
App::uses('Response', 'Network');
App::uses('ObjectRegistry', 'Utility');
App::import('Exceptions', 'Core');


/**
 * dispatcher
 * 
 * (default value: new Dispatcher())
 * 
 * @var mixed
 * @access public
 */
ObjectRegistry::storeObject('Request', new Request());
ObjectRegistry::storeObject('Response', new Response());


ObjectRegistry::storeObject(
	'Dispatcher', 
	new Dispatcher()
)->dispatch();

