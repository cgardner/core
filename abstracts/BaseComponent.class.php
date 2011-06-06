<?php
/**
 *  @package Cumula
 *  @subpackage Core
 *  @version    $Id$
 */

/**
 * The base class used for all extensible components.  This class inherits the EventDispatcher
 * allowing it to handle and dispatch events.
 * 
 * @package Cumula
 * @subpackage Core
 * @author Mike Reich
 *
 */
abstract class BaseComponent extends EventDispatcher {
	protected $rootDirectory;
	public $config;
	protected $_output;
	protected $_dataStore;
	
	/**
	 * Constructor.
	 * 
	 * @return unknown_type
	 */
	public function __construct() {
		$this->_registerEvents();
		parent::__construct();
		$this->_output = array();
		$this->config = new StandardConfig(ROOT.'/config', get_class($this).'.yaml');
		
		@$this->addEventListenerTo('ComponentManager', COMPONENT_STARTUP_COMPLETE, 'startup');
		$this->addEventListenerTo('Application', BOOT_SHUTDOWN, 'shutdown');

		$this->addEvent(EVENT_LOGGED);
	}

	
	/**
	 * Registers any constant defined in an 'events.inc' file in the component directory.
	 * 
	 * @return unknown_type
	 */
	protected function _registerEvents() {
		if(file_exists(static::rootDirectory() . '/events.inc')) {
			//Grab current consts
			$prev_consts = get_defined_constants(true);
			$prev_consts = $prev_consts['user'];
			
			//Pull in the consts defined in events.inc
			require_once static::rootDirectory() . '/events.inc';
			
			//Grab all defined consts plus new events in the current user space
			$new_consts = get_defined_constants(true);
			$new_consts = $new_consts['user'];
			
			//Find only the new consts added
			$consts = array_diff_assoc($new_consts, $prev_consts);
			
			//Iterate through and automatically register all new events
			foreach($consts as $name => $const) {
				$this->addEvent($const);
			}
		}
	}
	
	/**********************************************
	* Logging Functions
	***********************************************/
	
	/**
	 * @param $message
	 * @param $args
	 * @return unknown_type
	 */
	protected function _logInfo($message, $args = null) {
		$this->_logMessage(LOG_LEVEL_INFO, $message, $args);
	}
	
	/**
	 * @param $message
	 * @param $args
	 * @return unknown_type
	 */
	protected function _logDebug($message, $args = null) {
		$this->_logMessage(LOG_LEVEL_DEBUG, $message, $args);
	}

	/**
	 * @param $message
	 * @param $args
	 * @return unknown_type
	 */
	protected function _logError($message, $args = null) {
		$this->_logMessage(LOG_LEVEL_ERROR, $message, $args);
	}
	
	/**
	 * @param $message
	 * @param $args
	 * @return unknown_type
	 */
	protected function _logWarning($message, $args = null) {
		$this->_logMessage(LOG_LEVEL_WARN, $message, $args);
	}
	
	/**
	 * @param $message
	 * @param $args
	 * @return unknown_type
	 */
	protected function _logFatal($message, $args = null) {
		$this->_logMessage(LOG_LEVEL_FATAL, $message, $args);
	}
	
	/**
	 * @param $logLevel
	 * @param $message
	 * @param $other_args
	 * @return unknown_type
	 */
	protected function _logMessage($logLevel, $message, $other_args = null) {
		$className = get_called_class();
		$timestamp = date("r");
		$message = "$timestamp $className: $message";
		$args = array($logLevel, $message, $other_args);
		$this->dispatch(EVENT_LOGGED, $args);
	}
	
	/**********************************************
	* Component Callback Functions
	***********************************************/
	
	/**
	 * Run once when the module is first installed.
	 * 
	 * Placeholder function.  Should be overridden in client implementations to do anything.
	 * 
	 * @return unknown_type
	 */
	public function install() {
		
	}
	
	/**
	 * Run once when the module is uninstalled. TODO: Implement in ComponentManager
	 * 
	 * Placeholder function.  Should be overridden in client implementations to do anything.
	 * 
	 * @return unknown_type
	 */
	public function uninstall() {
		
	}
	
	/**
	 * Run when the module is enabled.
	 * 
	 * Placeholder function.  Should be overridden in client implementations to do anything.
	 * 
	 * @return unknown_type
	 */
	public function enable() {
		
	}
	
	/**
	 * Run when the module is disabled.
	 * 
	 * Placeholder function.  Should be overridden in client implementations to do anything.
	 * 
	 * @return unknown_type
	 */
	public function disable() {
		
	}
	
	
	/**
	 * Placeholder function.  Should be overridden in client implementations to do anything.
	 * 
	 * @return unknown_type
	 */
	public function startup() {
		
	}
	
	/**
	 * Placeholder function.  Should be overridden in client implementations to do anything.
	 * 
	 * @return unknown_type
	 */
	public function shutdown() {
		
	}
	
	/**********************************************
	* Rendering Functions
	***********************************************/
	
	/**
	 * Renders a specific filename, or a view with the filename matching the original function.  The 
	 * rendered content is sent to the templater as a block using the $var_name param.
	 * 
	 */
	public function render($file_name = null, $var_name = 'content') {
		if($file_name == null) {
			$bt = debug_backtrace(false); //TODO: See if there's a better way to do this than debug backtrace.
			$caller = $bt[1]['function'];
			$file_name = dirname($this->_getThisFile()).'/views/'.$caller.'.tpl.php';
		}
		$contents = $this->renderPartial($file_name);
		$this->_send_render($contents, $var_name);
	}
	
	/**
	 * Returns a rendered view specified in $file_name.  $args is exposed to the view.
	 * 
	 * @param $url
	 * @return unknown_type
	 */
	public function renderPartial($file_name = null, $args = array()) {
		$ext = '.tpl.php';
		if(pathinfo($file_name, PATHINFO_EXTENSION) == '' && !strpos($file_name, $ext)) {
			$file_name = dirname($this->_getThisFile()).'/views/'.$file_name.$ext;
		}
		extract($args, EXTR_OVERWRITE);
		ob_start();
		include $file_name;
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
	
	/**
	 * Adds a block to the render queue for dispatching to the templater.
	 * 
	 */
	protected function _send_render($content, $var_name = 'content') {
		$block = new ContentBlock();
		$block->content = $content;
		$block->data['variable_name'] = $var_name;
		$this->addOutputBlock($block);
	}
	
	
	/**
	 * @param $event
	 * @param $args
	 * @return unknown_type
	 */
	public function sendOutput($event, $args) {
		foreach($this->_output as $block) {
			$args[$block->data['variable_name']] = $block;
		}
	}

	/**
	 * Adds an output block to the templater
	 * 
	 * @param $block
	 * @return unknown_type
	 */
	public function addOutputBlock($block) {
		
		if(empty(Application::getResponse()->response['data'][$block->data['variable_name']]))
			Application::getResponse()->response['data'][$block->data['variable_name']] = array($block);
		else {
			Application::getResponse()->response['data'][$block->data['variable_name']][] = $block;
		}
	}
	
	/**********************************************
	* Utility Functions
	***********************************************/
	
	/**
	 * Convenience function to return the LSB instance.
	 * 
	 * @return unknown_type
	 */
	protected function _getThis() {
		return $this;
	}
	
	
	/**
	 * Returns the filepath of the basecomponent instance.
	 * 
	 */
	protected function _getThisFile() {
		$ref = new ReflectionClass(static::_getThis());
		return $ref->getFileName();
	}
	
	/**
	 * Redirects the client to the provided url.
	 * 
	 * @param $url
	 * @return unknown_type
	 */
	public function redirectTo($url) {
		Application::getResponse()->send302($url);
	}
	
	/**
	 * returns a url that includes the system base path
	 * 
	 * @param $url
	 * @return unknown_type
	 */
	public function linkTo($url) {
		$session = Application::getSystemConfig();
		$base = $session->getValue(SETTING_DEFAULT_BASE_PATH);
		return ($base == '/') ? $url : $base.$url;
	}
	
	/**
	 * Returns the system-wide default datastore setting
	 * 
	 * @return unknown_type
	 */
	public function defaultDataStore() {
		return Application::getSystemConfig()->getValue(SETTING_DEFAULT_DATASTORE);
	}
	
	
	/**
	 * Returns the root directory for the component.
	 * 
	 * @return unknown_type
	 */
	public function rootDirectory() {
		$class = new ReflectionClass(get_class($this));
		return dirname($class->getFileName());	
	}
}