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

require(__DIR__.DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."core.inc");


/**
 * Application Class
 *
 * The core application class.  This class does two main things:
 * 1) It initializes a few core classes, like the component_manager to handle plugins and extensiosn
 * 2) It works through a bootstrap proces which forms the core of the application lifecycle.
 *
 * @package		Cumula
 * @subpackage	Core
 * @author     Seabourne Consulting
 */
final class Application extends EventDispatcher {
	/**
	 * The boot process
	 * 
	 * @var array
	 */
	public $bootProcess = array(BOOT_INIT, 
						  	 	   BOOT_STARTUP, 
								   BOOT_PREPARE,
						   		   BOOT_PREPROCESS, 
						   		   BOOT_PROCESS, 
						   		   BOOT_POSTPROCESS, 
						   		   BOOT_CLEANUP, 
						   		   BOOT_SHUTDOWN);
						   	
	
	protected static $_request;
	protected static $_response;
	
	/**
	 * Constructor
	 * 
	 */
	public function __construct($startupCallback = null, $paths = null) {
		$this->_setupConstants($paths);
		$this->_setupBootstrap();
				
		parent::__construct();
		
		if(is_callable($startupCallback))
			call_user_func($startupCallback);
		
		$this->boot();
	}
	
	private function _setupConstants($paths = null) {
		if(!$paths) {
			$core_path	= 'core';
			$component_path = 'components';
			$config_path = 'config';
		} else {
			extract($paths);
		}


		define('ROOT', realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR));

		if(!is_dir($core_path) and is_dir(ROOT.$core_path))
			$core_path = ROOT.$core_path;

		if(!is_dir($component_path) and is_dir(ROOT.$component_path))
			$component_path = ROOT.$component_path;

		if(!is_dir($config_path) and is_dir(ROOT.$config_path))
			$config_path = ROOT.$config_path;

		define('APPROOT', realpath($core_path).DIRECTORY_SEPARATOR);
		define('COMPROOT', realpath($component_path).DIRECTORY_SEPARATOR);
		define('CONFIGROOT', realpath($config_path).DIRECTORY_SEPARATOR);
		define('CUMULAVERSION', "0.01");
	}
	
	/**
	 * Initializes the boot process by adding the individual steps as events
	 */
	private function _setupBootstrap() {
		foreach($this->bootProcess as $step) {
			$this->addEvent($step);
		}
	}
	
	/**
	 * Iterates through the boot process, triggering events for each.
	 */
	public function boot() {
		foreach($this->bootProcess as $step) {
			$this->dispatch($step, array(Request::getInstance(), Response::getInstance()));
		}
	}
	
	public static function __callStatic($name, $args) {
		if(strstr($name, 'get')) {
			$className = str_replace('get', '', $name);
			
			return call_user_func(array($className, 'getInstance'));
		}
	}
}