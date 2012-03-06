<?php
require_once('Controllers/AppController.php');
class ErrorsController extends AppController {
	
	public function index($params = null) {
		$this->viewFile = 'error';
		
		if(is_array($params)) {
			if(
				isset($params['error']['message'])
			) {
				
					$this->set('message', $params['error']['message']);
				
				
			} else {
				$this->set('message', '');
			}
		}
	}

}