<?php

class TextFileDataStore implements CumulaDataStore {
	
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