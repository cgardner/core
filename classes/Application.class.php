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

require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."core.inc");

/**
 * Application Class
 *
 * The core application class.  This class does two main things:
 * 
 * 1. It initializes a few core classes, like the component_manager to handle plugins and extensiosn
 * 2. It works through a bootstrap proces which forms the core of the application lifecycle.
 *
 * ### Events
 * The Application Class defines the following events:
 *
 * #### BOOT_INIT
 * The first part of the boot stage, BOOT_INIT can be used by any component that registers for startup treatment with the 
 * component manager.  BOOT_INIT should be used to initialize components.
 *
 * **Args**:
 * 
 * 1. **Request**: the Request object 
 * 2. **Response**: the Response object
 * 
 * #### BOOT_STARTUP
 * BOOT_STARTUP should be used to do startup tasks that are dependent on all classes being initialized and loaded into the 
 * global namespace.
 *
 * **Args**:
 * 
 * 1. **Request**: the Request object 
 * 2. **Response**: the Response object
 *
 * #### BOOT_PREPARE
 * BOOT_PREPARE should be used to collect information or generally prepare components for processing.
 *
 * **Args**:
 * 
 * 1. **Request**: the Request object 
 * 2. **Response**: the Response object
 *
 * #### BOOT_PREPROCESS
 * BOOT_PREPROCESS can be used to filter and/or adjust functionality before the request is processed.
 *
 * **Args**:
 * 
 * 1. **Request**: the Request object 
 * 2. **Response**: the Response object
 *
 * #### BOOT_PROCESS
 * BOOT_PROCESS should be used to run application logic and render content for display on the client browser.
 *
 * **Args**:
 * 
 * 1. **Request**: the Request object 
 * 2. **Response**: the Response object
 * 
 * #### BOOT_POSTPROCESS
 * BOOT_POSTPROCESS can be used for any cleanup that needs to happen, or filtering of rendered content.
 *
 * **Args**:
 * 
 * 1. **Request**: the Request object 
 * 2. **Response**: the Response object
 * 
 * #### BOOT_CLEANUP
 * BOOT_CLEANUP should be used by components to perform any actions that need to be done before the output is sent to the client.
 *
 * **Args**:
 * 
 * 1. **Request**: the Request object 
 * 2. **Response**: the Response object
 *
 * #### BOOT_SHUTDOWN
 * BOOT_SHUTDOWN signals that the output has been dispatched to the client.  This should be used to save settings or do any 
 * cleanup before the entire system is shutdown.
 *
 * **Args**:
 * 
 * 1. **Request**: the Request object 
 * 2. **Response**: the Response object
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
	
	private function _setupConstants($paths = array()) {
		if(count($paths) < 1) {
			$core_path	= 'core';
			$core_component_path = 'core/components';
			$contrib_component_path = 'components';
			$config_path = 'config';
			$data_path = 'data';
			$template_path = 'templates';
		} else {
			extract($paths);
		}


		define('ROOT', realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR));

		if(isset($core_path) && !is_dir($core_path))
			$core_path = ROOT.DIRECTORY_SEPARATOR.$core_path;

		if(isset($core_component_path) && !is_dir($core_component_path))
			$core_component_path = ROOT.DIRECTORY_SEPARATOR.$core_component_path;

		if(isset($config_path) && !is_dir($config_path)) {
			$config_path = ROOT.DIRECTORY_SEPARATOR.$config_path;
			if(!file_exists($config_path)) {
				mkdir($config_path, 0775, true);
			}
		}
			
		if(isset($data_path) && !is_dir($data_path)) {
			$data_path = ROOT.DIRECTORY_SEPARATOR.$data_path;
			if(!file_exists($data_path)) {
				mkdir($data_path, 0775, true);
			}
		}
		
		if(isset($contrib_component_path) && !is_dir($contrib_component_path)) {
			$contrib_component_path = ROOT.DIRECTORY_SEPARATOR.$contrib_component_path;
			if(!file_exists($contrib_component_path)) {
				mkdir($contrib_component_path, 0775, true);
			}
		}
			
		if(isset($template_path) && !is_dir($template_path))
			$template_path = ROOT.DIRECTORY_SEPARATOR.$template_path;	

		define('APPROOT', realpath($core_path).DIRECTORY_SEPARATOR);
        define('COMP_PATH', $core_component_path);
        define('CONTRIB_COMP_PATH', $contrib_component_path);
		define('COMPROOT', realpath($core_component_path).DIRECTORY_SEPARATOR);
		define('CONTRIBCOMPROOT', realpath($contrib_component_path).DIRECTORY_SEPARATOR);
		define('CONFIGROOT', realpath($config_path).DIRECTORY_SEPARATOR);
		define('DATAROOT', realpath($data_path).DIRECTORY_SEPARATOR);
		define('TEMPLATEROOT', realpath($template_path).DIRECTORY_SEPARATOR);
		define('CUMULAVERSION', "0.2.0");
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
