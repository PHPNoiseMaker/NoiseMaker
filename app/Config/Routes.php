<?php
/**
 * Route Definitions
 			
 			Example Base URL Route:
					Router::addRule('/', array(
						'controller' => 'Pages',
						'action' => 'index'
					));
			Example Custom Route:		
					Router::addRule(
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
					
					
			Example Default Route:
					Router::addRule('/:controller/:action/*');
 */
 

/**
 * Base URL Route
 * 
 * @var mixed
 * @access public
 */
Router::addRule('/', array(
	'controller' => 'Pages',
	'action' => 'index'
));

Router::addRule(
	'/page/:page_id/',
	array(
		'controller' => 'Pages',
		'action' => 'display'
	),
	array(
		'pass' => array('page_id')
	)
);


/**
 * Default Route (MUST BE LAST RULE!)
 * 
 * @var mixed
 * @access public
 */
Router::addRule('/:controller/:action/*');
