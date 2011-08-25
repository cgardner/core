<?php
namespace Cumula;

use \ReflectionClass as ReflectionClass;

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
 * ComponentManager Class
 *
 * The base class that handles loading components.
 *
 * This class hooks into the two initial phases of the boot process, BOOT_INIT and BOOT_STARTUP.
 * Module startup happens in two corresponding phases, first the files are loaded, then they are instantiated.
 *
 * BOOT_INIT is used to load the required files in the components directory.
 *
 * BOOT_STARTUP is used to actually instantiate the components.
 *
 * @package		Cumula
 * @subpackage	Core
 * @author     Seabourne Consulting
 */
final class ComponentManager extends BaseComponent {
	private $_components = array();
	private $_enabledClasses = array();
	private $_installedClasses = array();
	private $_availableClasses = array();
	private $_startupClasses = array();
	private $componentFiles = array();

	/**
	 * Constructor.
	 *
	 * @return unknown_type
	 */
	public function __construct() {
		parent::__construct();

		// Create new events for component management
		$this->addEvent('component_init_complete');
		$this->addEvent('component_startup_complete');

		// Set listeners for events
		$this->addEventListener('component_startup_complete', array(&$this, 'startup'));

		$this->addEventListenerTo('Application', 'boot_init', array(&$this, 'loadComponents'));
		$this->addEventListenerTo('Application', 'boot_startup', array(&$this, 'startupComponents'));
		$this->addEventListenerTo('Application', 'boot_shutdown', array(&$this, 'shutdown'));
		$this->addEventListenerTo('Cumula\\Autoloader', 'event_autoload', array($this, 'autoload'));


		// Initialize config and settings
		$this->config = new \StandardConfig\StandardConfig(CONFIGROOT, 'components.yaml');
		$this->loadSettings();

		// Set output
		$this->_output = array();
	}
	
    /**
     * Implementation of the getInfo method
     * @param void
     * @return array
     **/
    public static function getInfo() {
        return array(
            'name' => 'Component Manager',
            'description' => 'Componenet to manage other components',
            'version' => '0.1.0',
            'dependencies' => array(),
        );
    } // end function getInfo
	/**
	 * Implementation of the basecomponent startup function.
	 * 
	 */

	public function startup()
	{
		$this->addEventListenerTo('AdminInterface\\AdminInterface', 'admin_collect_settings_pages', 'setupAdminPages');
	}

	/**
	 * Populate the Autoloader
	 * @param string $event Name of the Event that was dispatched
	 * @param Cumula\Autoloader $dispatcher Object that dispatched the event
	 * @param string $className Name of the class being loaded
	 * @return void
	 **/
	public function autoload($event, $dispatcher, $className)
	{
		$dispatcher->registerClasses($this->getComponentFiles());
	} // end function autoload

	/**
	 * Defines and adds the admin pages to the admin interface, exposing the installed/enabled class lists.
	 * 
	 */
	public function setupAdminPages($event, $dispatcher) {
		$uninstalled = array_diff($this->_availableClasses, $this->_installedClasses);
		$page = $dispatcher->newAdminPage();
		$page->title = 'Components';
		$page->description = 'Below are the installed and enabled components in the system.';
		$page->route = '/admin/installed_components';
		$page->component = &$this;
		$page->callback = 'loadSettings';
		$page->fields = array(array('name' => 'enabled_components', 
			'title' => 'Enabled Components',
			'type' => 'checkboxes',
			'values' => $this->_installedClasses,
			'selected' => $this->_enabledClasses,
			'labels' => $this->_installedClasses),
		);
		$dispatcher->addAdminPage($page);
		
		/**
		 * If there are uninstalled components, show a menu item for those with the number of components in the title 
		 */

		$page = $dispatcher->newAdminPage();
		$page->title = 'New Components';
		if(count($uninstalled) > 0) {
			$componentNumber = ' <strong>'.count($uninstalled).'</strong>';
			$page->title .= $componentNumber;
		}
		$page->description = 'Below are the components available for installation.';
		$page->route = '/admin/new_components';				
		$page->component = &$this;
		$page->callback = 'installComponents';
		if (count($uninstalled) > 0)
		{
			$page->fields = array(array('name' => 'installed_components',
				'title' => 'Uninstalled Components',
				'type' => 'checkboxes',
				'values' => array_merge($uninstalled),
				'labels' => array_merge($uninstalled)
				));
		} else {
			$page->fields = array();
		}
		$dispatcher->addAdminPage($page);
		
	}
	
	/**
	 * Ensures that the installed and enabled components are saved on shutdown.
	 * 
	 */
	public function shutdown() {
		$this->config->setConfigValue('installed_components', $this->_installedClasses);
		$this->config->setConfigValue('enabled_components', $this->_enabledClasses);
	}

	/**
	 * Loads the saved settings, or if the first bootup, the default settings
	 * 
	 */
	public function loadSettings() {
		$this->_availableClasses = $this->_getAvailableComponents();
		$this->_installedClasses = array_intersect($this->_availableClasses, $this->config->getConfigValue('installed_components', array()));
		$this->_enabledClasses = array_intersect($this->_availableClasses, $this->config->getConfigValue('enabled_components', array()));
		$this->_startupClasses = array_intersect($this->_availableClasses, $this->config->getConfigValue('startup_components', array()));
	}

	/**
	 * Helper function to add a component to the startup list.
	 */
	public function registerStartupComponent($obj) {
		$this->_startupClasses[] = get_class($obj);
		$this->config->setConfigValue('startup_components', $this->_startupClasses);
	}
	
	/**
	 * Starts the defined startup components during the BOOT_INIT boot phase.
	 * 
	 * @param $url
	 * @return unknown_type
	 */
	public function startStartupComponents()
	{
		foreach ($this->getComponentFiles() as $className => $classFile)
		{
			$this->startupComponent($className);
		}
	}

	/**
	 * Helper function gathers the available components from the /components directory.
	 * 
	 * @param $url
	 * @return unknown_type
	 */
	protected function _getAvailableComponents()
	{
		return array_keys($this->getComponentFiles());
	}

	/**
	 * Iterates through the component directory and:
	 * 1) loads the component file
	 * 2) creates a record in the internal library array of the class.  This is used to instantiate the
	 *  components later.
	 * @return unknown_type
	 */
	public function loadComponents() {
		if (empty($this->_installedClasses)) {
			$this->installComponents($this->_availableClasses);
		}
		
		$this->parseComponentDir(COMPROOT);
		$this->parseComponentDir(CONTRIBCOMPROOT);

		$this->dispatch('component_init_complete');
	}
	
	protected function parseComponentDir($path) {
		$dir = dir($path);
		while (false !== ($comp = $dir->read())) {
			if(!strstr($comp, '.')) {
				$comp_dir = $dir->path.'/'.$comp;
				$class_name = ucfirst(basename($comp));
				$class_file = $comp_dir.'/'.$class_name.'.component';
				if (is_file($class_file) && (in_array($class_name, $this->_installedClasses)) && !class_exists($class_name)) {
				}
			}
		}
	}

	/**
	 * This function instantiates the components by iterating through the internal library array and creating
	 * new class instances for each entry.
	 *
	 * After all the components have been instantiated, the event COMPONENT_LOAD_COMPLETE is dispatched.
	 *
	 * @return unknown_type
	 */
	// TODO: How do we figure out if a component has been installed?
	// If it was installed programmatically or via the admin interface then the correct installation and enabling
	// process was executed. However, if a user manually edits the component yaml files we need a way to trigger
	// the install/enable functions for that component. Same for disabling/uninstalling components
	public function startupComponents() {
		if (empty($this->_enabledClasses)) {
			$this->installComponents($this->_installedClasses);
			$list = $this->_installedClasses;
		} else {
			$list = $this->_enabledClasses;
		}
		foreach($list as $class_name) {
			$this->startupComponent($class_name);
		}
		$this->dispatch('component_startup_complete');
	}

	/**
	 * Registers a new component instance in the internal registry.
	 *
	 * @param $component_class
	 * @return unknown_type
	 */
	public function startupComponent($component_class) {
		if(!isset($this->_components[$component_class]) && (in_array($component_class, $this->_enabledClasses)))
		{
			$this->_components[$component_class] = new $component_class();
		}
		else
		{
			return false;
		}
	}

	/**
	 * Given a class name, returns the matching instance.  If no matching instance is found, returns false.
	 *
	 * @param $className	string	 The classname to search for.
	 * @return unknown_type
	 */
	public function getComponentInstance($className) {
		if(isset($this->_components[$className]))
			return $this->_components[$className];
		else
			return false;
	}

	/**
   * Get the Files that should contain components
   * @param void
   * @return array
   **/
	public function getComponentFiles()
	{
		if (is_null($this->componentFiles) || count($this->componentFiles) == 0)
		{
			foreach(glob(sprintf('{%s*/*.component,%s*/*.component}', COMPROOT, CONTRIBCOMPROOT), GLOB_BRACE) as $file)
			{
				$basename = basename($file, '.component');
				$this->componentFiles[sprintf('%s\\%s', $basename, $basename)] = $file;
			}
		}
		return $this->componentFiles;
	} // end function getComponentFiles



	/*
	 * *************************************************************************
	 * *****************    ComponentManager API    ****************************
	 *
	 * *************************************************************************
	 */


	/**
	 * Installs a single component, based on the string $component parameter.
	 *
	 */
	public function installComponent($component) {
		$found = false;
		if (class_exists($component))
		{
			$reflection = new ReflectionClass($component);
			$class_file = $reflection->getFileName();
			$found = TRUE;
		}
		else
		{
			$dir = dir(COMPROOT);
			$comp_dir = $dir->path.'/'.$component;
			$class_file = $comp_dir.'/'.$component.'.component';
			if (is_file($class_file)) {
				$found = true;
			} else {
				$dir = dir(CONTRIBCOMPROOT);
				$comp_dir = $dir->path.'/'.$component;
				$class_file = $comp_dir.'/'.$component.'.component';
				if(is_file($class_file))
					$found = true;
			}
		}
		if ($found) {
			if(!in_array($component, $this->_installedClasses))
			{
				$this->_installedClasses[] = $component;
			}
			if(!in_array($component, $this->_enabledClasses))
			{
				$this->_enabledClasses[] = $component;
			}
			if(!array_key_exists($component, $this->_components))
			{
				$this->_components[$component] = new $component;
			}
			$instance = $this->_components[$component];
			$instance->install();
			$instance->installAssets();

			$this->_installedClasses[] = $component;
			$this->config->setConfigValue('installed_components', $this->_installedClasses);

			return $component;
		}

		return FALSE;
	}

	/**
	 * Installs an array of components.
	 */
	public function installComponents($components) {
		$installed_components = array();
		foreach($components as $component) {
			$installed = $this->installComponent($component);
			if ($installed) $installed_components[] = $component;
		}
		return $installed_components;
	}

	/**
	 * Installs all components in input component array and uninstalls any components not found in the input
	 * component list
	 *
	 */
	public function setInstalledComponents($components) {
		$uninstall_list = array_diff($this->_installedClasses, $components);
		$install_list = array_diff($components, $this->_installedClasses);
		foreach ($uninstall_list as $uninstall_component) {
			$instance = $this->getComponentInstance($uninstall_component);
			if ($instance) {
				if (in_array($uninstall_component, $this->_enabledClasses)) {
					$instance->disable();
					unset($this->_enabledClasses[$uninstall_component]);
				}
				$instance->uninstall();
				unset($this->_installedClasses[$uninstall_component]);

			}
		}

		// Set enabled and installed component config based off removed components
		$this->config->setConfigValue('enabled_components', $this->_enabledClasses);
		$this->config->setConfigValue('installed_components', $this->_installedClasses);

		$this->installComponents($install_list);
	}

	public function enableComponent($component) {
		if (in_array($component, $this->_enabledClasses)) {
			return FALSE;
		}

		$this->startupComponent($component);
		$instance = $this->getComponentInstance($component);
		if ($instance) {
			$instance->enable();
		}

		$this->_enabledClasses[] = $component;
		$this->config->setConfigValue('enabled_components', $this->_enabledClasses);

		return $component;
	}

	/**
	 * Setter for enabling components
	 * @return array of components that were enabled
	 */
	public function enableComponents($components) {
		$enabled_components = array();
		foreach ($components as $component) {
			$enabled = $this->enableComponent($component);
			if ($enabled) $enabled_components[] = $enabled;
		}
		return $enabled_components;
	}

	/**
	 * Takes an array of components and enables components not currently enabled while disabling installed components
	 * not in the input list
	 */
	public function setEnabledComponents($components) {
		$disable_list = array_diff($this->_installedClasses, $components);
		$enable_list = array_diff($components, $this->_enabledClasses);
		foreach($disable_list as $class_name) {
			$instance = $this->getComponentInstance($class_name);
			if($instance) {
				$instance->disable();
			}
		}
		$this->enableComponents($enable_list);
	}


	/**
	 * Getter for enabled components list
	 * @return array of enabled components
	 */
	public function getEnabledComponents() {
		return $this->_enabledClasses;
	}

	/**
	 * Getter for installed components list
	 * @return array of installed components
	 */
	public function getInstalledComponents() {
		return $this->_installedClasses;
	}

	/**
	 * Getter for startup components
	 * @return array of startup components
	 */
	public function getStartupComponents() {
		return $this->_startupClasses;
	}

	/**
	 * Getter for available components
	 * @return array of available components
	 */
	public function getAvailableComponents() {
		return $this->_availableClasses;
	}

}
