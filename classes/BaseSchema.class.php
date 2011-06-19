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
 * BaseSchema Class
 *
 * Abstract class used for describing a schema for a DataStore.
 *
 * @package		Cumula
 * @subpackage	Core
 * @author     Seabourne Consulting
 */
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