<?php
require_once('lib/Core/bootstrap.php');
App::import('Dispatcher', 'Router');
App::import('Request', 'Network');
App::import('Response', 'Network');


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

