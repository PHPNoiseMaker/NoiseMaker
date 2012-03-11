<?php

class App {
	public static function import($class, $path = null) {
		if(strpos('/', $path) !== false) {
			$path = implode(DS, explode('/', $path));
		}
		if(file_exists(ROOT . DS . APP_DIR . DS . $path . DS . $class . '.php')) {
			include APP_DIR . DS . $path . DS . $class . '.php';

		} elseif(file_exists(ROOT . DS . 'lib' . DS . $path . DS . $class . '.php')) {
		
			include 'lib' . DS . $path . DS . $class . '.php';
			
		} else {
			throw new ClassNotFoundException();
		}
		
		return true;
	
	}
}