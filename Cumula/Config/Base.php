<?php
namespace Cumula\Config;

/**
 * Base Config Interface
 * @package Cumual
 * @author Craig Gardner <craig@seabourneconsulting.com>
 **/
abstract class Base 
{
	abstract public function setConfigValue($key, $value);
	abstract public function getConfigValue($key);

	/**
	 * Set multiple configuration variables
	 * @param array $config Associative array of key/value pairs to save to the configuration
	 * @return void
	 **/
	public function setConfigValues(array $config = array()) 
	{
		foreach ($config as $key => $value)
		{
			$this->setConfigValue($key, $value);
		}
	} // end function setConfigValues
} // end class Base
