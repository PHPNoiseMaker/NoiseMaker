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
		
		$params = array(
			'error' => array(
				'message' => $e->getMessage(),
				'code' => $e->getCode()
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
		
		$params = array(
			'error' => array(
				'message' => $errorMsg,
				'code' => $errorNumber,
				'file' => $errorFile,
				'line' => $errorLine
			)
		);
		$controller->displayError($params);
		$controller->render('Errors');
		//exit;
	}
}