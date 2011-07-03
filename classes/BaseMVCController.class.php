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

/**
 * BaseMVCController Class
 *
 * The MVC Controller contains all application code for an MVC style component.
 *
 * @package		Cumula
 * @subpackage	Core
 * @author     Seabourne Consulting
 */
abstract class BaseMVCController extends EventDispatcher {
	public $component;
	
	protected $_before_filters = array();
	protected $_after_filters = array();
	protected $_template = 'template.tpl.php';
	protected $_data;
	protected $_alerts;
	protected $_renderCalled;
	
	/**
	 * The constructor.  Initializes the MVCContentBlock
	 * 
	 * @param $component
	 * @return unknown_type
	 */
	public function __construct($component) {
		$this->component = &$component;
		$this->startup();
		$this->_beforeFilter('_setTemplate');
		$this->_data = array();
		$this->_alerts = array('messages' => array(), 'warnings' => array());
		$this->_afterFilter('_doAlerts');
	}
	
  
	protected function setTemplateFile($file) {
    $this->_beforeFilter(function() {
			if($templater = Application::getTemplater())
				$templater->setTemplateFile('template.tpl.php');
		});
	}
	
  
	protected function _setTemplate() {
		if ($this->_template) {
			Application::getTemplater()->setTemplateDir(APPROOT.'/../templates/');
			Application::getTemplater()->setTemplateFile($this->_template);
		}	
	}
	
	protected function _doAlerts() {
		$messages = "<div class='message'>".implode("</div><div class='message'>", $this->_alerts['messages'])."</div>";
		$warnings = "<div class='message'>".implode("</div><div class='warning'>", $this->_alerts['warnings'])."</div>";
		$this->renderContent($messages, 'messages');
		$this->renderContent($warnings, 'warnings');
	}
	
	public function useTemplate($template) {
		$this->_template = $template;
	}
	
	/**
	 * Add a function to be run before the route handler is called.
	 * 
	 * @param $function The name of the function to be called
	 * @return unknown_type
	 */
	protected function _beforeFilter($function) {
		$this->_before_filters[] = $function;
	}
	
	/**
	 * Add a function to be run after the route handler is called.
	 * 
	 * @param $function  The name of the function to be called
	 * @return unknown_type
	 */
	protected function _afterFilter($function) {
		$this->_after_filters[] = $function;
	}
	
	/**
	 * Helper function for easily registering a route with the router.
	 * 
	 * @param $route
	 * @param $method
	 * @return unknown_type
	 */
	public function registerRoute($route, $method = null) {
		if(!$method) {
			$parts = explode('/', $route);
			$last = $parts[count($parts)-1];
			$method = $last;
		}
		$this->component->registerRoute($route, &$this, "____".$method);
	}

	/**
	 * Magic method to handle incoming route requests.  
	 * 
	 * @param $name
	 * @param $arguments
	 * @return unknown_type
	 */
	public function __call($name, $arguments) {
		$this->_renderCalled = false;
		$func = $this->_parseFunc($name);

		if($arguments[1] instanceof Router) {
			foreach($this->_before_filters as $filter) {
				//stop processing if the before filter returns false
				if($filter instanceof Closure) {
					if(call_user_func_array($filter, $arguments) === false)
						return;
				} else {
					if(method_exists($this, $filter) && is_callable(array(&$this, $filter)) && call_user_func_array(array(&$this, $filter), $arguments) === false)
						return;
				}
			}
		}
		
		$output = null;
		if(method_exists(static::_getThis(), $func)) {
			$output = call_user_func_array(array(static::_getThis(), $func), $arguments);
		}
		if(file_exists($this->getRenderFileName($func)) && !$this->_renderCalled)
			$this->render($func);
		
		
		foreach($this->_after_filters as $filter) {
			if(method_exists($this, $filter) && is_callable(array(&$this, $filter))) 
				call_user_func_array(array(&$this, $filter), $arguments);
		}
		
		return $output;
	}
	
	
	public function getRenderFileName($func) {
		$view_dir = $this->component->config->getConfigValue('views_directory', static::_getThis()->component->rootDirectory().'/views/'.lcfirst(str_replace('Controller', '', get_called_class())));
		return $view_dir.'/'.$func.'.tpl.php';
	}
	
	public function linkTo($title, $url, $args = array()) {
		$output = '<a href="'.$this->component->completeUrl($url).'" ';
		foreach($args as $key => $value) {
			$output .= $key.'="'.$value.'" ';
		}
		$output .= ">$title</a>";
		return $output;
	}
	
	/**
	 * Parses the passed magic method function into the final call.
	 * 
	 * @param $function
	 * @return unknown_type
	 */
	protected function _parseFunc($function) {
		return str_replace('____', '', $function);
	}

	/**
	 * Renders the view template file for the function.
	 * 
	 * @return unknown_type
	 */
	public function render() {
		$args = func_get_args();
		$this->_renderCalled = true;
		if(count($args) && is_string($args[0])) {
			$file_name = $this->getRenderFileName($args[0]);
	 	} else {
			$bt = debug_backtrace(false); //TODO: See if there's a better way to do this than debug backtrace.
			$caller = $bt[1];
			$file_name = $this->getRenderFileName($caller['function']);

			if(count($args) && count($args[0]))	
				extract($args[0], EXTR_OVERWRITE);
		}
		ob_start();
		include $file_name;
		$contents = ob_get_contents();
		ob_end_clean();
		$this->renderContent($contents);
	}
	
	/**
	 * Creates a new content block to contain $content, and adds it to the output queue.
	 * 
	 * @param $content
	 * @return unknown_type
	 */
	protected function renderContent($content, $name = 'content') {
		$block = new ContentBlock();
		$block->content = $content;
		$block->data['variable_name'] = $name;
		$this->component->addOutputBlock($block);
	}
	
	protected function renderPlain($output, $useTemplate = false, $contentType = 'text/plain') {
		if(($response = Response::getInstance()) && ($app = Application::getInstance())) {
			$response->response['content'] = $output;
			$response->response['headers']['Content-Type'] = $contentType;
			$app->removeEventListener(BOOT_POSTPROCESS, array(Templater::getInstance(), 'render'));
		}
	}
	
	protected function renderNothing() {
		if($app = Application::getInstance()) {
			$response->response['content'] = '';
			$app->removeEventListener(BOOT_POSTPROCESS, array(Templater::getInstance(), 'render'));
		}
	}
	
	/**
	 * Helper function for redirecting client to a new location.
	 * 
	 * @param $url The url to redirect to.
	 * @return unknown_type
	 */
	protected function redirectTo($url) {
		if(substr($url, 0, 1) == '/') {
			$config = Application::getSystemConfig();
			$base_path = $config->getValue(SETTING_DEFAULT_BASE_PATH, '');
			$url = $base_path.$url;
		}
		$this->component->redirectTo($url);
	}
	
	/**
	 * returns a url that includes the system base path
	 * 
	 * @param $url
	 * @return unknown_type
	 */
	public function completeUrl($url) {
		$session = Application::getSystemConfig();
		$base = $session->getValue(SETTING_DEFAULT_BASE_PATH);
		return ($base == '/') ? $url : $base.$url;
	}
	
	/**
	 * Helper function for returning the final static implementation of the class, using Late Static Bindings.
	 * 
	 * @return unknown_type
	 */
	protected function _getThis() {
		return $this;
	}
	
	public function __set($name, $value) {
		$this->_data[$name] = $value;
	}
	
	public function __get($name) {
		if(isset($this->_data[$name]))
			return $this->_data[$name];
	}
	
	public function __isset($name) {
		return isset($this->_data[$name]);
	}
	
	public function __unset($name) {
		if(isset($this->_data[$name]))
			unset($this->_data[$name]);
	}

	public function getInstanceVars() {
		return $this->_data;
	}
	
	protected function addWarning($warning) {
		$this->_alerts['warnings'][] = $warning;
	}
	
	protected function addMessage($message) {
		$this->_alerts['messages'][] = $message;
	}
	
	public function dispatch($event, $args) {
		$this->component->dispatch($event, $args);
	}
}