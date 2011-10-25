<?php
namespace Http;

/**
 * Base HTTP class
 * @package Cumula
 * @author Craig Gardner <craig@seabourneconsulting.com>
 **/
abstract class Base 
{
	/**
	 * Implementation of the magic __get method
	 * @param string $name Name of the property to get
	 * @return mixed
	 **/
	public function __get($name) 
	{
		var_dump($name);
		$methodName = sprintf('get%s', str_replace(' ', '', ucwords(str_replace('_', ' ', $name))));

		$methods = get_class_methods(get_class($this));
		if (in_array($methodName, $methods))
		{
			return $this->$method();
		}
		return FALSE;
	} // end function __get

	/**
	 * Implementation of the magic __set method
	 * @param string $name Name of the property to set
	 * @param mixed $value Value to set the property to
	 * @return void
	 **/
	public function __set($name, $value) 
	{
		$methodName = sprintf('set%s', str_replace(' ', '', ucwords(str_replace('_', ' ', $name))));
		$methods = get_class_methods(get_class($this));

		if (in_array($methodName, $methods))
		{
			$this->$methodName($value);
		}
	} // end function __set
} // end class 
