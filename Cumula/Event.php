<?php
namespace Cumula;

/**
 * Event Manager Class
 * @package Cumula
 * @author Craig Gardner <craig@seabourneconsulting.com>
 **/
class Event 
{
	/**
	 * Properties
	 */
	/**
	 * Array of events and the callbacks associated with them
	 * @var array
	 **/
	private static $eventTable = array();

	/**
	 * Public Methods
	 */
	/**
	 * Register an Event
	 * @param string $eventName Name of the event being registered
	 * @return boolean
	 **/
	public static function register() 
	{
		$args = func_get_args();
		if (isset($args[0]) && is_string($args[0])) 
		{
			list($eventName, $callback) = $args;
			if (!isset(self::$eventTable[$eventName]) || !in_array($callback, self::$eventTable[$eventName])) 
			{
				self::addEventCallback($eventName, $callback);
			}
		}
		elseif (isset($args[0]) && is_array($args[0]))
		{
			foreach ($args[0] as $eventName => $callback) 
			{
				// Skip the event if a proper event name wasn't given
				if (is_numeric($eventName))
				{
					continue;
				}

				self::addEventCallback($eventName, $callback);
			}
		}
	} // end function register

	/**
	 * Dispatch an Event
	 * @param string $eventName
	 * @return mixed
	 **/
	public static function dispatch($eventName) 
	{
		$args = func_get_args();
		array_shift($args);

		$bt = debug_backtrace();
		if (isset($bt[1]['object']))
		{
			array_unshift($args, $bt[1]['object'], $args);
		}

		$results = array();
		if (isset(self::$eventTable[$eventName])) 
		{
			foreach (self::$eventTable[$eventName] as $callback)
			{
				$results[] = call_user_func_array($callback, $args);
			}
		}
		return $results;
	} // end function dispatch

	/**
	 * Add a callback to the event registry
	 * @param string $eventName Name of the event
	 * @param callback $callback Callback for the event
	 **/
	private static function addEventCallback($eventName, $callback) 
	{
		$bt = debug_backtrace();
		$callerClass = isset($bt[2]['class']) ? $bt[2]['class'] : FALSE;
		$callerObject = isset($bt[2]['object']) ? $bt[2]['object'] : FALSE;
		if (!is_callable($callback))
		{
			if ($callerObject !== FALSE && is_callable(array($callerObject, $callback)))
			{
				$callback = array($callerObject, $callback);
			}
			else if ($callerClass !== FALSE && is_callable(array($callerClass, $callback))) 
			{
				$callback = array($callerClass, $callback);
			}
			else 
			{
				return;
			}
		}
		if (!isset(self::$eventTable[$eventName])) {
			self::$eventTable[$eventName] = array();
		}

		self::$eventTable[$eventName][] = $callback;
	} // end function addEventCallback

	/**
	 * Getters and Setters
	 */
} // end class Event
