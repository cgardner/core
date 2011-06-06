<?php
/**
 *  @package Cumula
 *  @subpackage Core
 *  @version    $Id$
 */

require_once dirname(__FILE__) . '/includes/sfYamlDumper.php';
require_once dirname(__FILE__) . '/includes/sfYamlParser.php';

/**
 * A basic data store using YAML.  A source directory and filename are passed in the config values and is used to save the
 * information in YAML format.
 * 
 * @author Mike Reich
 * @package Cumula
 *
 */
class YAMLDataStore extends BaseDataStore implements CumulaDataStore {
	private $_storage;
	private $_sourceDirectory;
	private $_filename;
	
	/**
	 * Accepts an array of config values as name => value pairs.  Two possible config values are:
	 *   -source_directory: the absolute file path to save the config file to
	 *   -filename: the YAML filename to save the information as
	 * 
	 * @param $config_values
	 * @return unknown_type
	 */
	public function __construct($config_values) {
		$this->_storage = array();
		$this->_sourceDirectory = $config_values['source_directory'];
		if(!file_exists($this->_sourceDirectory))
			mkdir($this->_sourceDirectory);
		$this->_filename = $config_values['filename'];
		
	}
	
	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#connect()
	 */
	public function connect() {
		$this->_load();
	}
	
	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#disconnect()
	 */
	public function disconnect() {
		$this->_save();
	}
	
	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#create($obj)
	 */
	public function create($obj) {
		foreach($obj as $key => $value) {
			$this->_storage[$key] = $value;
		}
		$this->_save();
	}
	
	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#update($obj)
	 */
	public function update($obj) {
		foreach($obj as $key => $value) {
			$this->_storage[$key] = $value;
		}
		$this->_save();
	}
	
	/**
	 * Creates or Updates an object depending on whether it exists already.
	 * 
	 * @param $obj
	 * @return unknown_type
	 */
	public function createOrUpdate($obj) {
		foreach($obj as $key => $value) {
			if ($this->recordExists($key)) {
				$this->update($obj);
			} else {
				$this->create($obj);
			}
		}
	}
	
	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#delete($obj)
	 */
	public function delete($obj) {
		if(is_string($obj)) {
			if ($this->recordExists($obj)) {
				unset($this->_storage[$obj]);
			}
		} else {
			foreach($obj as $key => $value) {
				unset($this->_storage[$key]);
			}
			$this->_save();
		}
	}
	
	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#query($args, $order, $sort)
	 */
	public function query($args, $order = null, $sort = null) {
		if ($this->recordExists($args)) {
			$obj = $this->_storage[$args];
		} else {
			$obj = null;
		}
		return $obj;
	}
	
	public function recordExists($id) {
		if(!isset($this->_storage))
			return false;
		return array_key_exists($id, $this->_storage);
	}
	
	/**
	 * Saves the data in the internal storage variable to the YAML file.
	 * @return unknown_type
	 */
	protected function _save() {
		if(!empty($this->_storage)) {
			$dumper = new sfYamlDumper();
			$yaml = $dumper->dump($this->_storage, 2);
			file_put_contents($this->_dataStoreFile(), $yaml);
		}
//			file_put_contents($this->_dataStoreFile(), Spyc::YAMLDump($this->_storage));
	}
	
	private function _dataStoreFile() {
		return $this->_sourceDirectory.'/'.$this->_filename;
	}
	
	/**
	 * Loads the data in the external YAML file into the internal storage var.
	 * 
	 * @return boolean True if the information was loaded, false otherwise.
	 */
	protected function _load() {
		if (file_exists($this->_dataStoreFile())) {
			$yaml = new sfYamlParser();
			$this->_storage = $yaml->parse(file_get_contents($this->_dataStoreFile()));
			//$this->_storage = Spyc::YAMLLoadString(file_get_contents($this->_dataStoreFile()));
			return true;
		} else {
			return false;
		}
	}
}