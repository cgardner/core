<?php

/**
 * Simple Schema Class
 * @package Cumula
 * @subpackage Core
 */
class SimpleSchema implements CumulaSchema {
    /**
     * Store the Fields for the Schema
     * @var array
     */
	protected $_storage;

    /**
     * Store the ID of the Schema
     * @var string
     */
	protected $_idField;

    /**
     * Unknown
     * @var mixed
     */
	protected $_migrations;

    /**
     * Name of the Schema
     * @var string
     */
	protected $_name;
	
    /**
     * Constructor
     * @param string $name
     * @param string $id
     * @param array $fields
     */
	public function __construct($name, $id = null, $fields = null) {
        $this->setName($name);
		$this->setIdField($id);
        $this->setFields($fields);
	}

    /**
     * Get the name of the schema
     * @return string
     */
	public function getName() {
		return $this->name;
	}
	
    /**
     * Set the Name of the Schema
     * @param string $name
     */
	public function setName($name) {
		$this->name = $name;
	}

    /**
     * Get the Fields for the Schema
     * @return array
     */
	public function getFields() {
		return $this->_storage;
	}
	
    /**
     * Set the Fields for the Schema
     * @param array $fields
     */
	public function setFields($fields) {
		$this->_storage = $fields;
	}
	
    /**
     * Get the ID Field for the Schema
     * @param void
     * @return string
     */
	public function getIdField() {
		return $this->_idField;
	}
	
    /**
     * Set the ID field for the Schema
     * @param string $id
     */
	public function setIdField($id) {
		$this->_idField = $id;
	}
	
	public function getObjInstance() {
		$obj = new stdClass();
		foreach($this->getFields() as $field => $type) {
			$obj->$field = null;
		}
		return $obj;
	}
}

