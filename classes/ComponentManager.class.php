<?php
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
	private $_classes = array();
	private $_enabledClasses = array();
	private $_installedClasses = array();
	private $_availableClasses = array();
	private $_startupClasses = array();

	/**
	 * Constructor.
	 *
	 * @return unknown_type
	 */
	public function __construct() {
		parent::__construct();
		//print_r(Application::getInstance());
		Application::getInstance()->addEventListener(BOOT_INIT, array(&$this, 'loadComponents'));
		Application::getInstance()->addEventListener(BOOT_STARTUP, array(&$this, 'startupComponents'));
		$this->addEvent(COMPONENT_INIT_COMPLETE);
		$this->addEvent(COMPONENT_STARTUP_COMPLETE);
		Application::getInstance()->addEventListener(BOOT_SHUTDOWN, array(&$this, 'shutdown'));

		$this->config = new StandardConfig(CONFIGROOT, 'components.yaml');

		$this->loadSettings();
		$this->_output = array();

		$this->addEventListener(COMPONENT_STARTUP_COMPLETE, array(&$this, 'startup'));
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
	public function startup() {
		if(Application::getAdminInterface())
			$this->addEventListenerTo('AdminInterface', ADMIN_COLLECT_SETTINGS_PAGES, 'setupAdminPages');
	}
	
	
	/**
	 * Defines and adds the admin pages to the admin interface, exposing the installed/enabled class lists.
	 * 
	 */
	public function setupAdminPages($event, $args = null) {
		$am = Application::getAdminInterface();
		if(!$am)
			return;
		$uninstalled = array_diff($this->_availableClasses, $this->_installedClasses);
		$page = $am->newAdminPage();
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
		$am->addAdminPage($page);
		
		/**
		 * If there are uninstalled components, show a menu item for those with the number of components in the title 
		 */

		$page = $am->newAdminPage();
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
		$am->addAdminPage($page);
		
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
		$this->_installedClasses = array_merge(array_intersect($this->_availableClasses, $this->config->getConfigValue('installed_components', array())));
		$this->_enabledClasses = array_merge(array_intersect($this->_availableClasses, $this->config->getConfigValue('enabled_components', array())));
		$this->_startupClasses = array_merge(array_intersect($this->_availableClasses, $this->config->getConfigValue('startup_components', array())));	
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
	public function startStartupComponents() {
        $files = $this->getComponentFiles();
        foreach ($files as $component) {
            require_once($component);
            $this->startUpComponent(basename($component, '.component'));
        }
	}

	/**
	 * Helper function gathers the available components from the /components directory.
	 * 
	 * @param $url
	 * @return unknown_type
	 */
	protected function _getAvailableComponents() {
		$ret = array();
        $files = $this->getComponentFiles();
        foreach ($files as $component) {
            $ret[] = basename($component, '.component');
        }
        return $ret;
	}

	/**
	 * Installs an array of components.
	 */
	public function installComponents($components) {
		foreach($components as $component) {
			$this->installComponent($component);
		}
	}

	/**
	 * Installs a single component, based on the string $component parameter.
	 * 
	 */
	public function installComponent($component) {
		$found = false;
		//TODO: replace the hard-coded components directory with a system config
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
		if($found) {
			require_once $class_file;
			if(!in_array($component, $this->_installedClasses))
			$this->_installedClasses[] = $component;
			if(!in_array($component, $this->_enabledClasses))
			$this->_enabledClasses[] = $component;
			if(!array_key_exists($component, $this->_components))
			$this->_components[$component] = new $component;
			$instance = $this->_components[$component];
			$instance->install();
		}
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
		
		//TODO: replace the hard-coded components directory with a system config
		$this->parseComponentDir(COMPROOT);
		$this->parseComponentDir(CONTRIBCOMPROOT);

		$this->dispatch(COMPONENT_INIT_COMPLETE);
	}
	
	protected function parseComponentDir($path) {
		$dir = dir($path);
		while (false !== ($comp = $dir->read())) {
			if(!strstr($comp, '.')) {
				$comp_dir = $dir->path.'/'.$comp;
				$class_name = ucfirst(basename($comp));
				$class_file = $comp_dir.'/'.$class_name.'.component';
				if (is_file($class_file) && (in_array($class_name, $this->_installedClasses)) && !class_exists($class_name)) {
					require_once $class_file;
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
		$this->dispatch(COMPONENT_STARTUP_COMPLETE);
	}

	/**
	 * Registers a new component instance in the internal registry.
	 *
	 * @param $component_class
	 * @return unknown_type
	 */
	public function startupComponent($component_class) {
		if(class_exists($component_class) &&
		!isset($this->_components[$component_class]) &&
		(in_array($component_class, $this->_enabledClasses))) {
			$this->_components[$component_class] = new $component_class();
		} else
			return false;
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
	 * Getter for returning the current list of component instances.
	 * 
	 */
	public function getComponentInstances() {
		return $this->_components;
	}

	/**
	 * Getter for returning the current list of installed components.
	 * 
	 */
	public function getInstalledComponentList() {
		return $this->_installedClasses;
	}

	/**
	 * Getter for returning the current list of available components.
	 * 
	 */
	public function getAvailableComponentList() {
		return $this->_availableClasses;
	}

	/**
	 * Setter for the installed component list
	 * 
	 */
	public function setInstalledComponentList($class_list) {
		$this->_installedClasses = $class_list;
		//$this->config->setConfigValue('installed_components', $this->_installedClasses);
	}

	/**
	 * Getter for returning the current list of enabled components.
	 * 
	 */
	public function getEnabledComponentList() {
		return $this->_enabledClasses;
	}

	/**
	 * Setter for the enabled component list
	 * 
	 */
	public function setEnabledComponentList($class_list) {
		$disabled_list = array_diff($this->_installedClasses, $class_list);
		$enabled_list = array_diff($class_list, $this->_enabledClasses);
		foreach($disabled_list as $class_name) {
			$instance = $this->getComponentInstance($class_name);
			if($instance)
				$instance->disable();
		}			
		$this->_enabledClasses = $class_list;
		foreach($enabled_list as $class_name) {
			$this->startupComponent($class_name);
			$instance = $this->getComponentInstance($class_name);
			if($instance)
				$instance->enable();
		}
		$this->config->setConfigValue('enabled_components', $this->_enabledClasses);
	}

	/**
	 * Getter for returning the current list of components initialized at startup.
	 * 
	 */
	public function getStartupComponentList() {
		return $this->_startupClasses;
	}

    /**
     * Get the Files that should contain components
     * @param void
     * @return array
     * @author Craig Gardner <craig@seabourneconsulting.com>
     **/
    public function getComponentFiles() {
        return glob(sprintf('{%s*/*.component,%s*/*.component}', COMPROOT, CONTRIBCOMPROOT), GLOB_BRACE);
        
    } // end function getComponentFiles
}
