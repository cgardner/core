<?php
namespace Cumula;

require_once realpath(dirname(__FILE__) .'/EventDispatcher.class.php');

/**
 * Cumula Autoloader
 * @package Cumula
 * @author Craig Gardner <craig@seabourneconsulting.com>
 **/
class Autoloader extends EventDispatcher
{
	/**
	 * Properties
	 */
	/**
	 * Autoload Event
	 * @var string
	 **/
	const EVENT_AUTOLOAD = 'event_autoload';

	/**
	 * Instance Variable
	 * @var Cumula\Autoloader
	 **/
	private static $instance;
	
	/**
	 * Cached Class map
	 * @var array
	 **/
	private $cache;
	
	/**
	 * Set up the autoloader
	 * @param void
	 * @return void
	 **/
	public static function setup() 
	{
		$class = 'Cumula\\Autoloader';
		spl_autoload_register(array($class, 'load'));
		$instance = self::getInstance();
		$instance->addEvent(self::EVENT_AUTOLOAD);
		$instance->addEventListenerTo($class, self::EVENT_AUTOLOAD, array($instance, 'defaultAutoloader'));
	} // end function setup

	/**
	 * Load a Autoload a class
	 * @param string $className Name of the class being loaded
	 * @return Cumula\Autoloader
	 **/
	public static function load($className) 
	{
		$instance = self::getInstance();
		// If we don't already know about the class, dispatch the event to find it.
		if (($classFile = $instance->classExists($className)) === FALSE)
		{
			$instance->dispatch(self::EVENT_AUTOLOAD, array($className));
			if (($classFile = $instance->classExists($className)) === FALSE)
			{
				return FALSE;
			}
		}
		require_once($classFile);
		return $instance;
	} // end function load

	/**
	 * Determine whether or not a class is in the autoloader
	 * @param string $className Name of the class being checked
	 * @return boolean
	 **/
	public function classExists($className) 
	{
		$cache = $this->getCache();
		return isset($cache[$className]) ? $cache[$className] : FALSE;
	} // end function classExists

	/**
	 * Default Autoloader Method
	 * @param void
	 * @return array Returns an associative array of classes and the files they are found in.
	 **/
	public function defaultAutoloader($event, $dispatcher, $className) 
	{
		$basedir = realpath(dirname(__FILE__) .'/../');
		$dir = realpath(dirname(__FILE__));
		$libDir = realpath($basedir .'/libraries/');
		$interfaceDir = realpath($basedir .'/interfaces/');
		$classes = array(
			// Core Classes
			'Cumula\\Application' => $dir. '/Application.class.php',
			'Cumula\\BaseComponent' => $dir .'/BaseComponent.class.php',
			'Cumula\\Request' => $dir .'/Request.class.php',
			'Cumula\\Response' => $dir .'/Response.class.php',
			'Cumula\\Router' => $dir .'/Router.class.php',
			'Cumula\\ComponentManager' => $dir .'/ComponentManager.class.php',
			'Cumula\\SimpleSchema' => $dir .'/SimpleSchema.class.php',
			'Cumula\\BaseDataStore' => $dir .'/BaseDataStore.class.php',
			'Cumula\\BaseSqlDataStore' => $dir .'/BaseSqlDataStore.class.php',
			'Cumula\\BaseMVCComponent' => $dir .'/BaseMVCComponent.class.php',
			'Cumula\\BaseMVCController' => $dir .'/BaseMVCController.class.php',
			'Cumula\\BaseMVCModel' => $dir .'/BaseMVCModel.class.php',
			'Cumula\\SystemConfig' => $dir .'/SystemConfig.class.php',

			// Exceptions
			'Cumula\\EventException' => $dir .'/Exception/EventException.class.php',
			'Cumula\\DataStoreException' => $dir .'/Exception/DataStoreException.class.php',

			// Interfaces
			'Cumula\\CumulaConfig' => $interfaceDir .'/CumulaConfig.interface.php',
			'Cumula\\CumulaSchema' => $interfaceDir .'/CumulaSchema.interface.php',
			'Cumula\\CumulaTemplater' => $interfaceDir .'/CumulaTemplater.interface.php',

			// Libraries
			'Cumula\\StandardConfig' => $libDir .'/StandardConfig/StandardConfig.class.php',
			'Cumula\\ContentBlock' => $libDir .'/ContentBlock/ContentBlock.class.php',
			'Cumula\\YAMLDataStore' => $libDir .'/YAMLDataStore/YAMLDataStore.class.php',
			'Cumula\\SqliteDataStore' => $libDir .'/SqliteDataStore/SqliteDataStore.class.php',
		);

		$dispatcher->registerClasses($classes);
	} // end function defaultAutoloader

	/**
	 * Register a class with the autoloader
	 * @param string $className Name of the class being registered
	 * @param string $classFile File where the class can be found
	 * @return Cumula\Autoloader
	 **/
	public function registerClass($className, $classFile) 
	{
		$cache = $this->getCache();
		if (!isset($cache[$className]))
		{
			$cache[$className] = $classFile;
			$this->setCache($cache);
		}
		elseif ($cache[$className] != $classFile)
		{
			throw new \Exception(sprintf('Trying to overwrite the Autoloader Cache for %s', $className));
		}
		return $this;
	} // end function registerClass

	/**
	 * Register multiple classes at once
	 * @param array $classArray Array of ClassName => ClassFile values
	 * @return void
	 **/
	public function registerClasses(array $classArray) 
	{
		$cache = $this->getCache();
		$this->setCache(array_merge($cache, $classArray));
	} // end function registerClasses

	/**
	 * Getters and Setters
	 */
	/**
	 * Getter for $this->cache
	 * @param void
	 * @return array
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	private function getCache() 
	{
		if (is_null($this->cache))
		{
			$this->setCache(array());
		}
		return $this->cache;
	} // end function getCache()
	
	/**
	 * Setter for $this->cache
	 * @param array
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	private function setCache($arg0) 
	{
		$this->cache = $arg0;
		return $this;
	} // end function setCache()

	/**
	 * Getter for $this->instance
	 * @param void
	 * @return Cumula\Autoloader
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public static function getInstance() 
	{
		if (is_null(self::$instance)) 
		{
			self::setInstance(new Autoloader());
		}
		return self::$instance;
	} // end function getInstance()
	
	/**
	 * Setter for $this->instance
	 * @param Cumula\Autoloader
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public static function setInstance(Autoloader $arg0) 
	{
		self::$instance = $arg0;
		return $arg0;
	} // end function setInstance()
} // end class Autoloader extends EventDispatcher
