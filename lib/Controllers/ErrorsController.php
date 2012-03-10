<?php
require_once 'app/Controllers/AppController.php';
/**
 * ErrorsController class.
 * 
 * @extends AppController
 */
class ErrorsController extends AppController {

	/**
	 * index function.
	 * 
	 * @access public
	 * @param mixed $params (default: null)
	 * @return void
	 */
	public function index($params = null) {
		$this->view = 'error';
		
		if(is_array($params)) {
			if(isset($params['error'])) {
				$this->set('error', $params['error']);

			} else {
				$this->set('error', '');
			}
			$this->set('url', $this->request->selfURL());
			
			if(array_key_exists('code', $params['error']))
				$this->response->_code = $params['error']['code'];
			else
				$this->response->_code = 500;
		}
	}

}