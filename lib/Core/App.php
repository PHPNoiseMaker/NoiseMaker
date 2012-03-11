<?php

class App {
	public static function import($class, $path = null, $check_only = false) {
		if(strpos('/', $path) !== false) {
			$path = implode(DS, explode('/', $path));
		}
		if(file_exists(ROOT . DS . APP_DIR . DS . $path . DS . $class . '.php')) {
			if(!$check_only)
				include_once APP_DIR . DS . $path . DS . $class . '.php';

		} elseif(file_exists(ROOT . DS . 'lib' . DS . $path . DS . $class . '.php')) {
			if(!$check_only)
				include_once 'lib' . DS . $path . DS . $class . '.php';
			
		} else {
			if(!$check_only) {
				switch($path) {
					case 'Controller':
						throw new ControllerNotFoundException();
						break;
					case 'Model':
						throw new ModelNotFoundException();
						break;
					default:
						throw new ClassNotFoundException();
						break;
				}
				
			}
			else
				return false;
		}
		
		return true;
	
	}
}