<?php
require_once('lib/Core/bootstrap.php');
require_once('lib/Router/Dispatcher.php');
require_once('lib/Network/Request.php');


/**
 * dispatcher
 * 
 * (default value: new Dispatcher())
 * 
 * @var mixed
 * @access public
 */
$dispatcher = new Dispatcher();
$dispatcher->dispatch(new Request());

