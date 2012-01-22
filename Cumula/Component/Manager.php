<?php
namespace Cumula\Component;
use Cumula\Event as Event;

/**
 * Cumula Component Manager
 * @package Cumula
 * @author Craig Gardner <craig@seabourneconsulting.com>
 **/
class Manager 
{
	/**
	 * Properties
	 */
	/**
	 * Array of Components that have been installed
	 * @var array
	 **/
	private $installedComponents;

	/**
	 * An array of the instantiated components
	 * @var array
	 **/
	private $components = array();

	/**
	 * Configuration Object
	 * @var Cumula\Config\Base
	 **/
	private $config;

	/**
	 * Public Methods
	 */
	/**
	 * Create a new Component Manager Object
	 **/
	public function __construct() 
	{
		Event::register(array(
			'boot_init' => 'loadComponents',
			'boot_startup' => 'startupComponents',
			'boot_shutdown' => 'shutdown',
		));
	} // end function __construct

	/**
	 * Load the Installed and Enabled Components
	 **/
	public function loadComponents() 
	{
		if (count($this->getComponents()) === 0)
		{
			$installedComponents = $this->getInstalledComponents();
			$components = array();
			foreach ($installedComponents as $class)
			{
				$instance = new $class();
				if ($instance instanceof \Cumula\Component\Base) 
				{
					$components[$class] = $instance;
				}
				else 
				{
					unset($instance);
				}
			}
			$this->setComponents($components);
		}
	} // end function loadComponents

	/**
	 * Run the startup method for all components
	 **/
	public function startupComponents() 
	{
		foreach ($this->getComponents() as $component)
		{
			$component->installAssets();
		}

		Event::dispatch('component_startup_complete');
	} // end function startupComponents

	/**
	 * Shutdown all components
	 **/
	public function shutdown() 
	{
		$this->getConfig()->setConfigValues(array(
			'installed_components' => $this->getInstalledComponents(),
		));
	} // end function shutdown

	/**
	 * Getters and Setters
	 */
	/**
	 * Getter for $this->components
	 * @param void
	 * @return array
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	private function getComponents() 
	{
		return $this->components;
	} // end function getComponents()
	
	/**
	 * Setter for $this->components
	 * @param array
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	private function setComponents($arg0) 
	{
		$this->components = $arg0;
		return $this;
	} // end function setComponents()
	/**
	 * Getter for $this->installedComponents
	 * @param void
	 * @return array
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function getInstalledComponents() 
	{
		if (is_null($this->installedComponents) || TRUE)
		{
			$this->installedComponents = array();
			$loaders = spl_autoload_functions();
			foreach ($loaders as $loader) 
			{
				if (is_array($loader) && is_callable(array($loader[0], 'getNamespace')) && is_callable(array($loader[0], 'getIncludePath')))
				{
					$componentsPath = implode(DIRECTORY_SEPARATOR, array(
						$loader[0]->getIncludePath(),
						$loader[0]->getNamespace(),
						'Components'
					));
					if (file_exists($componentsPath) && is_dir($componentsPath))
					{
						$components = glob($componentsPath . DIRECTORY_SEPARATOR .'*.php', GLOB_NOSORT);
						array_walk($components, function(&$item, $key, $ns) {
							$item = implode('\\', array(
								$ns,
								'Components',
								basename($item, '.php')));
						}, $loader[0]->getNamespace());
						$this->installedComponents += $components;
					}
				}
			}
		}
		return $this->installedComponents;
	} // end function getInstalledComponents()
	
	/**
	 * Setter for $this->installedComponents
	 * @param array
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function setInstalledComponents($arg0) 
	{
		$this->installedComponents = $arg0;
		return $this;
	} // end function setInstalledComponents()
	
	/**
	 * Getter for $this->config
	 * @param void
	 * @return sa
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function getConfig() 
	{
		if (is_null($this->config))
		{
			$this->config = new \Cumula\Config\Yaml(\Cumula\Application::getInstance()->getConfigDir(), 'components.yaml');
		}
		return $this->config;
	} // end function getConfig()
	
	/**
	 * Setter for $this->config
	 * @param sa
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function setConfig($arg0) 
	{
		$this->config = $arg0;
		return $this;
	} // end function setConfig()
} // end class Manager
