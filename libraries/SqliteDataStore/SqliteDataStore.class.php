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
 * SqliteDataStore Class
 *
 * Implementation of DataStore that uses an SQLite backend to save data.
 *
 * @package		Cumula
 * @subpackage	Core
 * @author     Seabourne Consulting
 */
class SqliteDataStore extends BaseSqlDataStore implements CumulaDataStore {
	protected $_db;
	
	public function __construct($config_values) {
		parent::__construct($config_values);
		if(!array_key_exists('schema', $config_values) || !is_a($config_values['schema'], 'CumulaSchema'))
			throw new Exception('Must pass a CumularSchema');
		$this->_storage = array();
		$this->_sourceDirectory = $config_values['source_directory'];
		if(!file_exists($this->_sourceDirectory))
			mkdir($this->_sourceDirectory);
		$this->_filename = $config_values['filename'];
		
		$this->_schema = $config_values['schema'];
		$this->_db = new SQLite3($this->_sourceDirectory.'/'.$this->_filename);
	}
	
	protected function doExec($sql) {
		return $this->_db->exec($sql);
	}
	
	protected function doQuery($sql) {
		return $this->_db->query($sql);
	}

	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#connect()
	 */
	public function connect() {
		//$this->_db->open($this->_sourceDirectory.'/'.$this->_filename);
		$this->_db->exec($this->install());
	}

	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#disconnect()
	 */
	public function disconnect() {
		$this->_db->close();
	}

	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#query($args, $order, $sort)
	 */
	public function query($args, $order = null, $sort = null) {
		$result = parent::query($args, $order, $sort);
		$arr = array();
		if(!$result)
			return false;
		while($res = $result->fetchArray(SQLITE3_ASSOC)) {
			$arr[] = $res;
		}
		
		if (count($arr) == 1)
			return $arr[0];
		else if(count($arr) == 0)
			return false;
		else
			return $arr;
	}

	public function recordExists($id) {
		return $this->query($id);
	}
	
	public function translateFields($fields) {
		$return = array();
		foreach($fields as $field => $args) {
			$new_args = array();
			switch($args['type']) {
				case 'string':
					$new_args['type'] = 'TEXT';
					//$new_args['size'] = "(".(array_key_exists('size', $args) ? $args['size'] : 255).")";	
					break;
				case 'integer':
					$new_args['type'] = 'INTEGER';
				//	$new_args['size'] = "(".(array_key_exists('size', $args) ? $args['size'] : 11).")";	
					break;
				case 'float':
					$new_args['type'] = 'REAL';
					//$new_args['size'] = "(".(array_key_exists('size', $args) ? $args['size'] : 11).")";	
					break;
				case 'boolean':
					$new_args['type'] = 'INTEGER';
					//$new_args['size'] = "(".(array_key_exists('size', $args) ? $args['size'] : 1).")";	
				case 'text':
					$new_args['type'] = 'TEXT';
				//	$new_args['size'] = "(".(array_key_exists('size', $args) ? $args['size'] : null).")";	
					break;
				case 'datetime':
					$new_args['type'] = 'TEXT';
					//$new_args['size'] = "(".(array_key_exists('size', $args) ? $args['size'] : null).")";	
					break;
				case 'blob':
					$new_args['type'] = 'TEXT';
					//$new_args['size'] = "(".(array_key_exists('size', $args) ? $args['size'] : null).")";	
					break;
			}
			if(array_key_exists('default', $args))
				$new_args['default'] = " DEFAULT ".is_numeric($args['default']) ? $args['default'] : "'{$args['default']}'";
			if(array_key_exists('autoincrement', $args))
				$new_args['autoincrement'] = ' PRIMARY KEY ';
			if(array_key_exists('primary', $args))
				$new_args['primary'] = ' PRIMARY KEY ';				
			if(array_key_exists('null', $args))
				$new_args['null'] = " NOT NULL ";	
			$return[$field] = $new_args;
		}
		return $return;
	}
}