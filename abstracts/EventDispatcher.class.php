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
 * in the Cumula Framework.
 *
 * @package		Cumula
 * @subpackage	Core
 * @author     Seabourne Consulting
 */
abstract class EventDispatcher {
	protected static $_instances = array();
	
	protected $_eventTable = array();
	
	/**
	 * Constructor
	 * 
	 * @return unknown_type
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
	 * @param $event
	 * @return unknown_type
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
	 * @param $event
	 * @return unknown_type
	 */
	public function removeEvent($event) {
		if ($this->eventExists($event)) {
			unset($this->_eventTable[$event]);
		}
	}
	
	/**
	 * Adds a handler to be called when a specific event is dispatched.  This function accepts two parameters.
	 * 
	 * @param $event	string	The event to bind to
	 * @param $handler	a function, or a class/method or an anonymous function.  Uses the same syntax as call_user_func_array.
	 * @return unknown_type
	 */
	public function addEventListener($event, $handler) {
		if ($this->eventExists($event)) {
			$this->_eventTable[$event][] = $handler;
		}
	}
	
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
	 * @param $event
	 * @param $handler
	 * @return unknown_type
	 */
	public function removeEventHandler($event, $handler) {
		//TODO: Implement using an array slice type function
	}
	
	/**
	 * Dispatches an event.  Data must be an array of variables that will be passed to any registered event handler.
	 * 
	 * @param $event
	 * @param $data
	 * @return unknown_type
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
	 * @param $event
	 * @return unknown_type
	 */
	public function eventExists($event) {
		return array_key_exists($event, $this->_eventTable);
	}	

	protected static function getInstance() {
		return isset(self::$_instances[get_called_class()]) ? self::$_instances[get_called_class()] : false;
	}
	
	public static function setInstance($instance) {
		self::$_instances[get_class($instance)] = $instance;
	}
}