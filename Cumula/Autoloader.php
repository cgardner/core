<?php
namespace Cumula;

/**
 * Autoloader Class
 * @package Cumula
 * @author Craig Gardner <craig@seabourneconsulting.com>
 **/
class Autoloader 
{
	/**
	 * Properties
	 */
	/**
	 * Namespace used for this autoloader
	 * @var string
	 **/
	private $namespace;
	
	/**
	 * Include Path for this namespace
	 * @var string
	 **/
	private $includePath;

	/**
	 * Namespace Separator
	 * @var string
	 **/
	private $namespaceSeparator = '\\';
	
	/**
	 * Extension to use for Autoloaded Classes
	 * @var string
	 **/
	private $fileExtension = '.php';
	
	private $loadedClasses = array();

	/**
	 * Public Methods
	 */
	/**
	 * Creates a new Autoloader that loads the classes of the specified namespace
	 * 
	 * @param string $ns The namespace to use.
	 * @param string $includePath The path to use for loading classes
	 * @return void
	 **/
	public function __construct($ns = NULL, $includePath = NULL) 
	{
		$this->namespace = $ns;
		$this->setIncludePath($includePath);
	} // end function __construct

	/**
	 * Register this class in the spl_autoloader
	 **/
	public function register() 
	{
		spl_autoload_register(array($this, 'loadClass'));
	} // end function register

	/**
	 * Unregister this class in the spl_autoloader
	 **/
	public function unregister() 
	{
		spl_autoload_unregister(array($this, 'loadClass'));
	} // end function unregister

	/**
	 * Load the given class or interface
	 * @param string $className Name of the class to load
	 * @return void
	 **/
	public function loadClass($className) 
	{
		if (!in_array($className, $this->loadedClasses, TRUE)) 
		{
			if ($this->namespace === NULL || $this->namespace . $this->namespaceSeparator === substr($className, 0, strlen($this->namespace . $this->namespaceSeparator))) 
			{
				$fileName = '';
				$namespace = '';
				if (($lastNsPos = strripos($className, $this->namespaceSeparator)) !== FALSE)
				{
					$namespace = substr($className, 0, $lastNsPos);
					$className = substr($className, $lastNsPos + 1);
					$fileName = str_replace($this->namespaceSeparator, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
				}
				$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . $this->fileExtension;
				require ($this->includePath !== NULL ? $this->includePath . DIRECTORY_SEPARATOR : '') . $fileName;
				$this->loadedClasses[] = $className;
			}
		}
	} // end function loadClass

	/**
	 * Getters and Setters
	 */
	/**
	 * Getter for $this->fileExtension
	 * @param void
	 * @return string
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function getFileExtension() 
	{
		return $this->fileExtension;
	} // end function getFileExtension()
	
	/**
	 * Setter for $this->fileExtension
	 * @param string
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function setFileExtension($arg0) 
	{
		$this->fileExtension = $arg0;
		return $this;
	} // end function setFileExtension()
	
	/**
	 * Getter for $this->namespaceSeparator
	 * @param void
	 * @return string
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function getNamespaceSeparator() 
	{
		return $this->namespaceSeparator;
	} // end function getNamespaceSeparator()
	
	/**
	 * Setter for $this->namespaceSeparator
	 * @param string
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function setNamespaceSeparator($arg0) 
	{
		$this->namespaceSeparator = $arg0;
		return $this;
	} // end function setNamespaceSeparator()
	
	/**
	 * Getter for $this->includePath
	 * @param void
	 * @return string
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function getIncludePath() 
	{
		return $this->includePath;
	} // end function getIncludePath()
	
	/**
	 * Setter for $this->includePath
	 * @param string
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function setIncludePath($arg0) 
	{
		$this->includePath = $arg0;
		return $this;
	} // end function setIncludePath()
	
	/**
	 * Getter for $this->namespace
	 * @param void
	 * @return string
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function getNamespace() 
	{
		return $this->namespace;
	} // end function getNamespace()
	
	/**
	 * Setter for $this->namespace
	 * @param string
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function setNamespace($arg0) 
	{
		$this->namespace = $arg0;
		return $this;
	} // end function setNamespace()
} // end class Autoloader
