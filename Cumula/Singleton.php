<?php
namespace Cumula;

/**
 * Base Singleton Class for Cumula
 * @package Cumula
 * @author Craig Gardner <craig@seabourneconsulting.com>
 **/
abstract class Singleton 
{
	/**
	 * Properties
	 */
	/**
	 * Singleton Instance Variable
	 * @var Object
	 **/
	private static $instance;

	/**
	 * Public Methods
	 */
	/**
	 * Create a new Singleton Instance
	 **/
	public function __construct() 
	{
		self::$instance = $this;
	} // end function __construct

	/**
	 * Get the Singleton Instance
	 * @return Cumula\Singleton
	 **/
	public static function getInstance() 
	{
		if (is_null(self::$instance))
		{
			$class = get_called_class();
			self::$instance = new $class();
		}
		return self::$instance;
	} // end function getInstance
} // end class Singleton
