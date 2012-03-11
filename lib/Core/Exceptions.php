<?php
/**
 * Exceptions file.
 *
 *	Borrowed from CakePHP (AWESOME Framework!)
 * 		Please go check them out; Without them this project would not be possible.
 *		I have learned a great deal from their code, website and coding style.
 *		
 *
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/view/1196/Testing CakePHP(tm) Tests
 * @package       Cake.Error
 * @since         CakePHP(tm) v 2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Exception Definitions
 */
if (!class_exists('HttpException')) {
	class HttpException extends RuntimeException {
	}
}

class BadRequestException extends HttpException {

	public function __construct($message = null, $code = 400) {
		if (empty($message)) {
			$message = 'Bad Request';
		}
		parent::__construct($message, $code);
	}

}


class UnauthorizedException extends HttpException {

	public function __construct($message = null, $code = 401) {
		if (empty($message)) {
			$message = 'Unauthorized';
		}
		parent::__construct($message, $code);
	}

}

class ForbiddenException extends HttpException {

	public function __construct($message = null, $code = 403) {
		if (empty($message)) {
			$message = 'Forbidden';
		}
		parent::__construct($message, $code);
	}

}


class NotFoundException extends HttpException {

	public function __construct($message = null, $code = 404) {
		if (empty($message)) {
			$message = 'Not Found';
		}
		parent::__construct($message, $code);
	}

}

class ViewNotFoundException extends HttpException {

	public function __construct($message = null, $code = 404) {
		if (empty($message)) {
			$message = 'View File Not Found';
		}
		parent::__construct($message, $code);
	}

}
class LayoutNotFoundException extends HttpException {

	public function __construct($message = null, $code = 404) {
		if (empty($message)) {
			$message = 'View File Not Found';
		}
		parent::__construct($message, $code);
	}

}
class ControllerNotFoundException extends HttpException {

	public function __construct($message = null, $code = 404) {
		if (empty($message)) {
			$message = 'Controller Not Found';
		}
		parent::__construct($message, $code);
	}

}

class ModelNotFoundException extends HttpException {

	public function __construct($message = null, $code = 404) {
		if (empty($message)) {
			$message = 'Model Not Found';
		}
		parent::__construct($message, $code);
	}

}

class ClassNotFoundException extends HttpException {

	public function __construct($message = null, $code = 404) {
		if (empty($message)) {
			$message = 'Class Not Found';
		}
		parent::__construct($message, $code);
	}

}


class MethodNotAllowedException extends HttpException {

	public function __construct($message = null, $code = 405) {
		if (empty($message)) {
			$message = 'Method Not Allowed';
		}
		parent::__construct($message, $code);
	}

}


class InternalErrorException extends HttpException {


	public function __construct($message = null, $code = 500) {
		if (empty($message)) {
			$message = 'Internal Server Error';
		}
		parent::__construct($message, $code);
	}

}
