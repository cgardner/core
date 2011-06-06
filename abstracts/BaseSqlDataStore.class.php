<?php

abstract class BaseSqlDataStore extends BaseDataStore implements CumulaDataStore {
	protected $_db;
	
	public function __construct($config_values) {
		parent::__construct($config_values);
	}
	
	protected function doExec($sql) {
		
	}
	
	protected function doQuery($sql) {
		
	}

	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#create($obj)
	 */
	public function create($obj) {
		$sql = "INSERT INTO {$this->_schema->name} ";
		$keys = array();
		$values = array();
		foreach($obj as $key => $value) {
			$keys[] = $key;
			$values[] = is_numeric($value) ? "$value" : "'".$this->_db->escapeString($value)."'";
		}
		$sql .= "(".implode(',', $keys).")";
		$sql .= "VALUES (".implode(',', $values).");";
		return $this->doExec($sql);
	}
	
	public function install() {
		$sql_output = "CREATE TABLE IF NOT EXISTS {$this->_schema->name}(";
		$fields = array();
		foreach(static::translateFields($this->_schema->getFields()) as $field => $attrs) {
			$field = "$field {$attrs['type']}";
			if(array_key_exists('size', $attrs))
				$field .= $attrs['size'];
			if(array_key_exists('default', $attrs))
				$field .= $attrs['default'];
			if(array_key_exists('autoincrement', $attrs))
				$field .= $attrs['autoincrement'];
				
			$fields[] = $field;	
		}
		$sql_output .= implode(', ', $fields).');';
		return $sql_output;
	}
	
	public function uninstall() {
		return "DROP TABLE {$this->_schema->name}";
	}

	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#update($obj)
	 */
	public function update($obj) {
		$idField = $this->_schema->getIdField();
		if(!$this->recordExists($obj->$idField))
			return false;
		$sql = "UPDATE {$this->_schema->name} SET ";
		foreach($obj as $key => $value) {
			$sql .= " $key=" . is_numeric($value) ? "$value" : "'".$this->_db->escapeString($value)."'";
		}
		$sql .= " WHERE {$idField}=".$obj->$idField.";";
		return $this->doExec($sql);
	}

	/**
	 * Creates or Updates an object depending on whether it exists already.
	 *
	 * @param $obj
	 * @return unknown_type
	 */
	public function createOrUpdate($obj) {
		$idField = $this->_schema->getIdField();
		if($this->query($obj->$idField))
			return $this->update($obj);
		else
			return $this->create($obj);
	}

	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#delete($obj)
	 */
	public function delete($obj) {
		$idField = $this->_schema->getIdField();
		$sql = "DELETE FROM {$this->_schema->name} WHERE ";
		if(is_numeric($obj))
			$sql .= "$idField=$obj;";
		else
			$sql .= "$idField={$obj->$idField};";
		return $this->doExec($sql);
	}

	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#query($args, $order, $sort)
	 */
	public function query($args, $order = null, $sort = null) {
		$sql = "SELECT * FROM {$this->_schema->name} WHERE ";
		//Args is an id
		if (is_numeric($args)) {
			$sql .= "{$this->_schema->getIdField()}=$args";
		} else if (is_array($args)) {
			foreach($args as $key => $value) {
				$sql .= "$key=".(is_numeric($value) ? $value : "'{$this->_db->escapeString($value)}'");
			}
		} else {
			//no parsible arguments found
			return false;
		}
		$sql .= ';';
		return $this->doQuery($sql);
	}

	public function recordExists($id) {
	}
}