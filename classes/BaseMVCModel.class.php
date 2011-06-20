<?php

abstract class BaseMVCModel extends EventDispatcher {
	protected static $_fields;
	protected $_data;
	protected static $_dataStore = array();
	protected $_fieldsToSerialize = array();
	
	abstract public static function setupDataStore();
	
	abstract public static function setupFields();
	
	public function __construct($args = array()) {
		parent::__construct();
		$fields = static::getFields();
		foreach($fields as $field => $data) {
			if(isset($args[$field]))
				$this->$field = $args[$field];
		}
	}
	
	public function serialize($fields) {
		if(is_array($fields))
			$this->_fieldsToSerialize = $fields;
		else if(is_string($fields))
			$this->_fieldsToSerialize[] = $fields;
	}
	
	public static function find($args) {
		$res = static::getDataStore()->query($args);
		$class = get_called_class();
		for($i = 0; $i < count($res); $i++) {
			$res[$i] = new $class($res[$i]);
		}
		if(count($res) == 1)
			return $res[0];
		else
			return $res;
	}
	
	public static function findAll() {
		return static::getDataStore()->query(array());
	}
	
	public static function getDataStore() {
		$class = get_called_class();
		if(!isset(self::$_dataStore[$class]))
			self::$_dataStore[$class] = static::setupDataStore();
		
		return self::$_dataStore[$class];
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
		static::getDataStore()->destroy($this->rawObject());
	}
	
	public function create() {
		static::getDataStore()->create($this->rawObject());
	}
	
	public function update() {
		static::getDataStore()->createOrUpdate($this->rawObject());
	}
	
	public function getSchema() {
		//implemented by children classes
	}
	
	public function __get($name) {
		if(isset($this->_data[$name])) {
			$val = $this->_data[$name];
			if(in_array($name, $this->_fieldsToSerialize)) {
				return unserialize($val);
			}
			return $var;
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
	
	public function rawObject() {
		$obj = new stdClass();
		foreach($this->_data as $key => $value) {
			if(in_array($key, $this->_fieldsToSerialize)){
				$value = serialize($value);
			}
			$obj->$key = $value;
		}
		return $obj;
	}
}