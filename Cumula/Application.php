<?php
namespace Cumula;

/**
 * Cumula Application Class
 * @package Cumula
 * @author Craig Gardner <craig@seabourneconsulting.com>
 **/
class Application extends Singleton
{
	/**
	 * Properties
	 */
	/**
	 * Application Root Directory
	 * @var string
	 **/
	private $appRoot;

	/**
	 * Application Asset Directory
	 * @var string
	 **/
	private $assetDir;

	/**
	 * Boot Process
	 * @var array
	 **/
	private $bootProcess = array(
		'boot_init', 
		'boot_startup', 
		'boot_prepare',
		'boot_preprocess', 
		'boot_process', 
		'boot_postprocess', 
		'boot_cleanup', 
		'boot_shutdown',
	);
	
	/**
	 * Public Methods
	 */
	/**
	 * Create a new Cumula Application Instance
	 * @param void
	 * @return void
	 **/
	public function __construct($env, $callback = NULL) 
	{
		parent::__construct();
		$bt = debug_backtrace();
		$this->appRoot = dirname(dirname($bt[0]['file']));

		if (isset($callback) && is_callable($callback)) {
			$callback($this);
		}

		$this->boot($this->bootProcess);
	} // end function __construct
 
	/**
	 * Boot the Application
	 * @param array $steps Boot Steps to be performed
	 **/
	private function boot(array $steps) 
	{
		foreach ($steps as $step)
		{
			Event::dispatch($step, $this);
		}
	} // end function boot
	/**
	 * Get the Public Asset Directory
	 * @return string
	 **/
	public function getAssetDir() 
	{
		if (!isset($this->assetDir)) {
			$this->assetDir = implode(DIRECTORY_SEPARATOR, array(
				$this->appRoot,
				'public',
				'assets'
			));;
		}
		return $this->assetDir;
	} // end function getAssetDir
	/**
	 * Getters and Setters
	 */
	/**
	 * Getter for $this->appRoot
	 * @param void
	 * @return string
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function getAppRoot() 
	{
		return $this->appRoot;
	} // end function getAppRoot()
	
	/**
	 * Setter for $this->appRoot
	 * @param string
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function setAppRoot($arg0) 
	{
		$this->appRoot = $arg0;
		return $this;
	} // end function setAppRoot()
	
} // end class Application
