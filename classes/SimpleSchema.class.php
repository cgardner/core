<?php

class SimpleSchema implements CumulaSchema {
	protected $_storage;
	protected $_idField;
	protected $_migrations;
	protected $_name;
	
	public function __construct($name, $id = null, $fields = null) {
		$this->name = $name;
		$this->_idField = $id;
		$this->_storage = $fields;
	}

	public function getName() {
		return $this->_name;
	}
	
	public function setName($name) {
		$this->_name = $name;
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

