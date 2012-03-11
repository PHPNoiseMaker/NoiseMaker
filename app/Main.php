<?php
require_once('lib/Core/bootstrap.php');
App::uses('Dispatcher', 'Router');
App::uses('Request', 'Network');
App::uses('Response', 'Network');


/**
 * dispatcher
 * 
 * (default value: new Dispatcher())
 * 
 * @var mixed
 * @access public
 */
$dispatcher = new Dispatcher(new Request(), new Response());
$dispatcher->dispatch();

