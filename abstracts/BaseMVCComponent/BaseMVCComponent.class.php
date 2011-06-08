<?php
/**
 * Cumula
 *
 * Cumula — framework for the cloud.
 *
 * @package    Cumula
 * @version    0.1.0
 * @author     Seabourne Consulting
 * @license    MIT License
 * @copyright  2011 Seabourne Consulting
 * @link       http://cumula.org
 */

require_once dirname(__FILE__) . '/BaseMVCController.php';

/**
 * BaseMVCComponent Class
 *
 * The core class for the MVC component architecture.
 *
 * @package		Cumula
 * @subpackage	Core
 * @author     Seabourne Consulting
 */
abstract class BaseMVCComponent extends BaseComponent {
	protected $_routes;
	
	
	/**
	 * Constructor.  All events and handlers should be definied in the constructor.
	 * 
	 * @return unknown_type
	 */
	public function __construct() {
		parent::__construct();
		$this->_routes = array();
	}
	
	/**
	 * Initialization function.  Loads the controller and model files for the component.
	 * @return unknown_type
	 */
	public function startup() {
		//load component classes
		$this->_loadFiles($this->config->getConfigValue('controller_dir', '/controllers'));
		$this->_loadFiles($this->config->getConfigValue('model_dir', '/models'));
		
		Application::getRouter()->addEventListener(ROUTER_COLLECT_ROUTES, array(&$this, 'routes'));
	}
	
	/**
	 * Route handler function respondes to Router::ROUTER_COLLECT_ROUTES
	 * 
	 * @return unknown_type
	 */
	public function routes() {
		Application::getRouter()->addRoutes($this->_routes);
	}
	
	/**
	 * elper function for registering a route with the router.
	 * 
	 * @param $route
	 * @param $controller
	 * @param $method
	 * @param $args
	 * @return unknown_type
	 */
	public function registerRoute($route, $controller, $method, $args = array()) {
		$this->_routes[$route] = array(array(&$controller, $method), $args);	
	}
	
	/**
	 * Loads the php classes located in a given directory
	 * 
	 * @param $file_dir
	 * @return unknown_type
	 */
	protected function _loadFiles($file_dir) {
		$combined_dir = static::rootDirectory().$file_dir;
		if(!file_exists($combined_dir))
			return;
		$dir = dir($combined_dir);
		while (false !== ($comp = $dir->read())) {
			if(substr($comp, 0, 1) != '.') {
				$comp_dir = $dir->path.'/'.$comp;
				$class_name = str_replace('.class.php', '', $comp);
				if (is_file($comp_dir)) {
					require_once $comp_dir;
					new $class_name($this);					
				}
			}
		}
	}
}