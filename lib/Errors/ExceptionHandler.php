<?php

/**
 * ExceptionHandler class.
 */
class ExceptionHandler {
	/**
	 * handleException function. handles all exceptions thrown (unless otherwise caught)
	 * 
	 * @access public
	 * @static
	 * @param Exception $e
	 * @return void
	 */
	public static function handleException(Exception $e) {
		App::uses('ErrorsController', 'Controller');
		$controller = new ErrorsController(new Request(), new Response());
		
		$debugLevel = Config::getConfig('debug');
		if($debugLevel > 0) {
			$message = $e->getMessage();
			$code = $e->getCode();
		} else {
			$message = 'Not Found';
			$code = 404;
		}
		$params = array(
			'error' => array(
				'message' => $message,
				'code' => $code
			)
		);
		$controller->displayException($params);
		$controller->render('Errors');
		//exit;
	}
	
	/**
	 * handleError function. Handles all errors thrown
	 * 
	 * @access public
	 * @static
	 * @param mixed $errorNumber
	 * @param mixed $errorMsg
	 * @param mixed $errorFile
	 * @param mixed $errorLine
	 * @return void
	 */
	public static function handleError($errorNumber, $errorMsg, $errorFile, $errorLine) {
		App::uses('ErrorsController', 'Controller');
		$controller = new ErrorsController(new Request(), new Response());
		
		$debugLevel = Config::getConfig('debug');
		if($debugLevel > 0) {
			$params = array(
				'error' => array(
					'message' => $errorMsg,
					'code' => $errorNumber,
					'file' => $errorFile,
					'line' => $errorLine
				)
			);
		} else {
			$params = array(
				'error' => array(
					'message' => 'Not Found',
					'code' => 404
				)
			);

		}
		
		$controller->displayError($params);
		$controller->render('Errors');
		//exit;
	}
}