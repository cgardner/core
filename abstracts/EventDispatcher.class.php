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
 * EventDispatcher Class
 *
 * The base class that handles event registration and dispatching.  This serves as the base class for most classes
 * in the Cumula Framework
 *
 * ### Events
 * The EventDispatcher defines the following events:
 *
 * #### EVENTDISPATCHER_EVENT_DISPATCHED
 * This is a type of meta-event, dispatched whenever another event is dispatched to a particular listener.  If there are 
 * multiple listeners for an event, this event will be dispatched multiple times.
 *
 * **Args**:
 * 
 * 1. **Event**: the event dispatched.
 * 2. **Dispatcher**: the original dispatcher.
 * 3. **Event Listener**: the listener the event was dispatched to.
 * 4. **Level**: the event stack level
 *
 * @package		Cumula
 * @subpackage	Core
 * @author     Seabourne Consulting
 */
abstract class EventDispatcher {
	protected static $_instances = array();
	
	protected $_eventTable = array();
	
	/**
	 * Constructor.  Sets the default global $level to 0.
	 */
	public function __construct() {
		self::setInstance($this);
		
		global $level;
		if(!isset($level))
			$level = 0;
		$this->addEvent(EVENTDISPATCHER_EVENT_DISPATCHED);		
	}
	
	/**
	 * Registers an event in the internal registry.  Raises an exception if trying to re-register an existing event.  This ensures
	 * that components don't unwittingly use the same event title.
	 * 
	 * @param	string	The event to add to the registry.
	 */
	public function addEvent($event) {
		if ($this->eventExists($event))
			throw new Exception('Event Already Exists.  You are trying to register an event that has previously been registered.');
		else
			$this->_eventTable[$event] = array();
	}
	
	/**
	 * Removes an event from the registry table.
	 * 
	 * @param	string	The event to remove from the registry.
	 */
	public function removeEvent($event) {
		if ($this->eventExists($event)) {
			unset($this->_eventTable[$event]);
		}
	}
	
	/**
	 * Adds a handler to be called when a specific event is dispatched.  This function accepts two parameters.
	 * 
	 * @param	string	The event to bind to
	 * @param	function	a function, or an array containing the class and method, or a closure.  Uses the same syntax as call_user_func_array.
	 */
	public function addEventListener($event, $handler) {
		if ($this->eventExists($event)) {
			$this->_eventTable[$event][] = $handler;
		}
	}
	
	/**
	 * Adds a handler to be called when a specific event is dispatched.  This function accepts two parameters.
	 * 
	 * @param	string	The class to bind to
	 * @param	string	The event to bind to
	 * @param	function|string	A string or closure.  If a string, must be a publicly accessible function in the EventDispatcher instance.
	 */
	public function addEventListenerTo($class, $event, $function) {
		if(!class_exists($class))
			trigger_error('Tried to bind to an event for a class that does not exist.', E_USER_WARNING);
		if(is_string($function)) {
			$callback = array($this, $function);
		} else if (is_callable($function)) {
			$callback = $function;
		}
		$instance = call_user_func(array($class, 'getInstance'));
		if($instance)
			return $instance->addEventListener($event, $callback);
		else
			trigger_error("Tried to bind event to class $class which has not yet been instantiated");
	}
	
	/**
	 * Given an event and handler, removes any matching entry in the event registry
	 * 
	 * @param	string The event to remove handler from.
	 * @param	function	a function, or an array containing the class and method, or a closure to remove.
	 */
	public function removeEventHandler($event, $handler) {
		//TODO: Implement using an array slice type function
	}
	
	/**
	 * Dispatches an event.  Data must be an array of variables that will be passed to any registered event handler.
	 * 
	 * @param	string	The event to dispatch
	 * @param	array 	An optional array or arguments to pass to the Event Listeners
	 */
	public function dispatch($event, $data = array()) {
		if ($this->eventExists($event)) {
			array_unshift($data, $event, &$this);
			global $level;
			if ($event != EVENTDISPATCHER_EVENT_DISPATCHED)
				$level++;
			//For each listener call the handler function	
			foreach($this->_eventTable[$event] as $event_handler) {
				//Fire of an EVENT_DISPATCHED event if there are active listeners
				if ($event != EVENTDISPATCHER_EVENT_DISPATCHED) {	
					$this->dispatch(EVENTDISPATCHER_EVENT_DISPATCHED, array($event, $this, $event_handler, $level));
				}
				call_user_func_array($event_handler, $data);
			}
			if ($event != EVENTDISPATCHER_EVENT_DISPATCHED)
				$level--;
			return true;
		} else {
			return false;
		}
	}
	 
	/**
	 * Verifies that an event exists in the internal registry.
	 * 
	 * @param	string	The event to check
	 * @return bool	True or false depending on whether the event exists.
	 */
	public function eventExists($event) {
		return array_key_exists($event, $this->_eventTable);
	}	

	/**
	 * Returns the instance of the static class.
	 * 
	 * @return BaseComponent|bool	The instance, if it exists, otherwise false
	 */
	protected static function getInstance() {
		return isset(self::$_instances[get_called_class()]) ? self::$_instances[get_called_class()] : false;
	}
	
	/**
	 * Sets the instance of a class
	 * 
	 * @param	BaseComponent	The instance to set.
	 */
	public static function setInstance($instance) {
		self::$_instances[get_class($instance)] = $instance;
	}
}