<?php
require_once('lib/Core/bootstrap.php');
require_once('lib/Router/Dispatcher.php');


$dispatcher = new Dispatcher();
$dispatcher->dispatch();

