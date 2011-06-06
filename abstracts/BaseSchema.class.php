<?php

class BaseSchema implements CumulaSchema {
	protected $_storage;
	protected $_idField;
	protected $_migrations;
	
	public $name;
	
	public function __construct() {
		$this->_idField = null;
		$this->_storage = null;
	}

	public function getFields() {
		return $this->_storage;
	}
	
	public function setFields($fields) {
		$this->_storage = $fields;
	}
	
	public function getIdField() {
		return $this->_idField;
	}
	
	public function setIdField($id) {
		$this->_idField = $id;
	}
}