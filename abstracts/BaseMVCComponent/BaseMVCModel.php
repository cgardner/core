<?php

abstract class BaseMVCModel extends EventDispatcher {
	protected static $_fields;
	protected $_data;
	
	public function __construct($args = array()) {
		parent::__construct();
		$fields = static::getFields();
		foreach($fields as $field => $data) {
			if(isset($args[$field]))
				$this->$field = $args[$field];
		}
	}
	
	public static function find($args) {
		return static::getDataStore()->query($args);
	}
	
	public static function getDataStore() {
		return false;
	}
	
	public static function setupFields() {
		return false;
	}
	
	public static function addField($fieldName, $type, $args = array()) {
		$class = get_called_class();
		$args['type'] = $type;
		if(!isset(self::$_fields[$class]))
			self::$_fields[$class] = array();
		self::$_fields[$class][$fieldName] = $args;
	}
	
	public static function getFields() {
		static::setupFields();
		$class = get_called_class();
		return self::$_fields[$class];
	}
	
	public function save() {
		$this->update();
	}
	
	public function destroy() {
		static::getDataStore()->destroy($this);
	}
	
	public function create() {
		static::getDataStore()->create($this);
	}
	
	public function update() {
		static::getDataStore()->createOrUpdate($this);
	}
	
	public function getSchema() {
		//implemented by children classes
	}
	
	public function __get($name) {
		if(isset($this->_data[$name])) {
			return $this->_data[$name];
		}
	}
	
	public function __isset($name) {
		return isset($this->_data[$name]);
	}
	
	public function __set($name, $value) {
		$this->_data[$name] = $value;
	}
	
	public function __unset($name) {
		unset($this->_data[$name]);
	}
}