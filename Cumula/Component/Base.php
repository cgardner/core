<?php
namespace Cumula\Component;
use Cumula\Event as Event;

/**
 * Base Component Class
 * @package Cumula
 * @author Craig Gardner <craig@seabourneconsulting.com>
 **/
abstract class Base 
{
	/**
	 * Properties
	 */
	/**
	 * Asset Directory
	 * @var string
	 **/
	protected $assetDir;

	/**
	 * Public Asset Directory
	 * @var string
	 **/
	protected $publicAssetDir;

	/**
	 * Public Methods
	 */
	/**
	 * Create a new Component Instance
	 **/
	public function __construct() 
	{
		if (is_callable(array($this, 'startup')))
		{
			Event::register('component_startup_complete', 'startup');
		}
		
		if (is_callable(array($this, 'shutdown')))
		{
			Event::register('boot_shutdown', 'shutdown');
		}
	} // end function __construct

	/**
	 * Get the Asset Directory of this component
	 * @return string Path of the Asset Directory
	 **/
	public final function getAssetDir() 
	{
		if (is_null($this->assetDir))
		{
			$reflection = new \ReflectionClass(get_class($this));
			$fileName = $reflection->getFileName();
			if(($extStart = strripos($fileName, '.')) !== FALSE)
			{
				$assetDir = substr($fileName, 0, $extStart) . DIRECTORY_SEPARATOR . 'Assets';
				return is_dir($assetDir) ? $assetDir : FALSE;
			}
		}
		return FALSE;
	} // end function getAssetDir

	/**
	 * Get the Public Asset Directory for the component
	 * @param void
	 * @return string Path of the Public Asset Directory for this component
	 **/
	public final function getPublicAssetDir() 
	{
		if (is_null($this->publicAssetDir))
		{
			$this->publicAssetDir = \Cumula\Application::getInstance()->getAssetDir() . DIRECTORY_SEPARATOR . str_replace('\\Components\\', DIRECTORY_SEPARATOR, get_class($this));
		}
		return $this->publicAssetDir;
	} // end function getPublicAssetDir

	/**
	 * Install the Assets for the component
	 **/
	public final function installAssets() 
	{
		if (($assetDir = $this->getAssetDir()) !== FALSE && ($publicAssetDir = $this->getPublicAssetDir()) !== FALSE)
		{
			// Don't continue if there are no assets to copy
			if (is_dir($assetDir))
			{
				// If the public asset directory doesn't exist, create it recursively
				if (!is_dir($publicAssetDir))
				{
					mkdir($publicAssetDir, 0777, TRUE);
				}	

				$this->copyAssetFiles($assetDir, $publicAssetDir);
			}
		}
	} // end function installAssets


	/**
	 * Private Methods
	 */
	/**
	 * Recursive function to re-create the filestructure in the
	 * component's asset directory in the public asset directory
	 * @param string $source
	 * @param string $destination
	 * @return void
	 **/
	private function copyAssetFiles($source, $destination) 
	{
		if (is_dir($source)) 
		{
			// Find all of the files in the directory and create directories
			// for the subdirectories
			foreach(glob($source .'/*', GLOB_NOSORT) as $file) 
			{
				$dirname = basename($file);
				$newDestination = $destination . DIRECTORY_SEPARATOR . $dirname;
				if (is_dir($file) && is_dir($newDestination) === FALSE) 
				{
					mkdir($newDestination, 0777, TRUE);
				}
				$this->copyAssetFiles($file, $newDestination);
			}
		}
		else 
		{
			// Copy the file to the public assets directory
			copy($source, $destination);
		}
	} // end function copyAssetFiles
} // end abstract Base
