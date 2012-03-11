<?php

class App {
	public static function import($class, $path = null, $check_only = false) {
		if(strpos('/', $path) !== false) {
			$path = implode(DS, explode('/', $path));
		}
		if(file_exists(ROOT . DS . APP_DIR . DS . $path . DS . $class . '.php')) {
			if(!$check_only)
				include APP_DIR . DS . $path . DS . $class . '.php';

		} elseif(file_exists(ROOT . DS . 'lib' . DS . $path . DS . $class . '.php')) {
			if(!$check_only)
				include 'lib' . DS . $path . DS . $class . '.php';
			
		} else {
			if(!$check_only)
				throw new ClassNotFoundException();
			else
				return false;
		}
		
		return true;
	
	}
}