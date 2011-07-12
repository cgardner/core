<?php

abstract class BaseMVCModel extends EventDispatcher {
	protected static $_fields;
	protected $_data;
	protected static $_dataStore = array();
	protected $_fieldsToSerialize = array();
	protected $exists = false;
	
	abstract public static function setupDataStore();
	
	abstract public static function setupFields();
	
	public function __construct($args = array(), $exists = false) {
		parent::__construct();
		if(!is_array($args)) {
			$args = (array)$args;
		}
		$fields = static::getFields();
		foreach($fields as $field => $data) {
			if(isset($args[$field]))
				$this->$field = $args[$field];
		}
		$this->exists = $exists;
	}
	
	public function serialize($fields) {
		if(is_array($fields))
			$this->_fieldsToSerialize = $fields;
		else if(is_string($fields))
			$this->_fieldsToSerialize[] = $fields;
	}
	
	public static function find($args, $order = array(), $limit = null) {
		$res = static::getDataStore()->query($args, $order, $limit);
		$class = get_called_class();
		if($res && is_array($res)) {
			for($i = 0; $i < count($res); $i++) {
				$res[$i] = new $class($res[$i], true);
			}
			return $res;
		} else {
			return false;
		}	
	}
  
  
  public static function findOne($args) {
		$res = static::getDataStore()->query($args, null, 1);
		$class = get_called_class();
		if($res && is_array($res)) {
			for($i = 0; $i < count($res); $i++) {
				$res[$i] = new $class($res[$i], true);
			}
			return $res[0];
		} else {
			return false;
		}	
	}
  
	
	public static function findAll() {
		$res = static::find(array());
		if(!is_array($res))
			$res = array($res);
		return $res;
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
		return $this->update();
	}
	
	public function destroy() {
		$res = static::getDataStore()->destroy($this->rawObject());
		if($res)
			$this->exists = false;
		return $res;
	}
	
	public function create() {
		$res = static::getDataStore()->create($this->rawObject());
		if($res) {
			$id = $this->_schema->getIdField();
			$this->$id = $this->_dataStore->lastRowId();
			$this->exists = true;
		}
		return $res;
	}
	
	public function update() {
		return static::getDataStore()->createOrUpdate($this->rawObject());
	}
	
	public function getSchema() {
		//implemented by children classes
	}
	
	public function __get($name) {
		if(isset($this->_data[$name])) {
			$val = $this->_data[$name];
			if(in_array($name, $this->_fieldsToSerialize) && is_string($val)) {
				return unserialize($val);
			}
			return $val;
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