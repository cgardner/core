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
 * TextFileDataStore Class
 *
 * Implementation of DataStore that saves data in a flat text file.
 *
 * @package		Cumula
 * @subpackage	Core
 * @author     Seabourne Consulting
 */

class TextFileDataStore extends BaseDataStore {
	
	private $_logFile;
	
	public function __construct($config_values) {
		$this->_storage = array();
		$this->_logFile = $config_values['logfile'];
	}
	
	public function connect() {
		$this->_load();
	}
	
	public function disconnect() {
		$this->_save();
	}
	
	public function create($obj) {
	
	}
	
	public function update($obj) {
		
	}
	
	public function createOrUpdate($obj) {
		
	}

	public function delete($obj) {
		
	}
	
	public function query($args, $order = null, $sort = null) {

	}
	
	public function recordExists($id) {
		
	}
}