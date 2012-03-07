<?php
/**
 * Route Definitions
 			
 			Example Base URL Route:
					$this->router->addRule('/', array(
						'controller' => 'Pages',
						'action' => 'index'
					));
			Example Custom Route:		
					$this->router->addRule(
						'/page/:page_id/view',
						array(
							'controller' => 'Pages',
							'action' => 'display'
						),
						array(
							'pass' => array(
								'page_id'
							)
						)
					);
 */
 

/**
 * Base URL Route
 * 
 * @var mixed
 * @access public
 */
$this->router->addRule('/', array(
	'controller' => 'Pages',
	'action' => 'index'
));


/**
 * Default Route (MUST BE LAST RULE!)
 * 
 * @var mixed
 * @access public
 */
$this->router->addRule('/:controller/:action/*');
