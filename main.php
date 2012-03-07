<?php
require_once('lib/Core/bootstrap.php');
require_once('lib/Router/Dispatcher.php');


/**
 * dispatcher
 * 
 * (default value: new Dispatcher())
 * 
 * @var mixed
 * @access public
 */
$dispatcher = new Dispatcher();
$dispatcher->dispatch();

