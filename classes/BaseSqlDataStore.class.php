<?php
namespace Cumula;
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
 * BaseSQLDataStore Class
 *
 * Abstract Class for all SQL derived Data Stores.  Takes care of some of the common code, like creating tables and CRUD operations.
 *
 * @package		Cumula
 * @subpackage	Core
 * @author     Seabourne Consulting
 */

abstract class BaseSqlDataStore extends BaseDataStore {
	protected $_db;
	/**
	 * Schema Object Used for this DataStore
	 * @var SimpleSchema
	 **/
	private $schema;

	abstract public function escapeString($dirtyString);
	
	public function __construct($schema, $config_values) {
		parent::__construct($schema, $config_values);
	}
	
	protected function doExec($sql) {
		
	}
	
	protected function doQuery($sql) {
		
	}

	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#create($obj)
	 */
	public function create($obj) {
		$sql = "INSERT INTO {$this->getSchema()->name} ";
		$keys = array();
		$values = array();
		foreach($this->getSchema()->getFields() as $field => $args) {
			if(!isset($obj->$field))
				continue;
			$keys[] = $field;
			$values[] = is_numeric($obj->$field) ? $obj->$field : $this->escapeString($obj->$field);
		}
		$sql .= "(".implode(',', $keys).")";
		$sql .= "VALUES (".implode(',', $values).");";
		return $this->doExec($sql);
	}
	
	public function install() {
		$sql_output = "CREATE TABLE IF NOT EXISTS {$this->getSchema()->name}(";
		$fields = array();
		foreach(static::translateFields($this->getSchema()->getFields()) as $field => $attrs) {
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
		return "DROP TABLE {$this->getSchema()->name}";
	}

	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#update($obj)
	 */
	public function update($obj) {
		$idField = $this->getSchema()->getIdField();
		if(!$this->recordExists($obj->$idField))
			return false;
		$sql = "UPDATE {$this->getSchema()->name} SET ";
		$fields = array();
		foreach($this->getSchema()->getFields() as $field => $args) {
			if(property_exists($obj, $field)) {
				$fields[] = " $field=" . (is_numeric($obj->$field) ? $obj->$field : "'".$this->escapeString($obj->$field)."'");
			}
		}
		$sql .= implode(", ", $fields);
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
		$idField = $this->getSchema()->getIdField();
		if(isset($obj->$idField) && $this->query($obj->$idField)) {
			return $this->update($obj);
        } else {
            $create = $this->create($obj);
            if($create) {
                $id = $this->lastRowId();
                return $id;
            } else {
                return FALSE;
            }
        }
	}

	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#delete($obj)
	 */
	public function destroy($obj) {
		$idField = $this->getSchema()->getIdField();
		$sql = "DELETE FROM {$this->getSchema()->name} WHERE ";
		if(is_numeric($obj))
			$sql .= $idField.' = '.$obj.';';
		else
			$sql .= $idField.' = "'.$obj->$idField.'";';
		return $this->doExec($sql);
	}

	/* (non-PHPdoc)
	 * @see core/interfaces/DataStore#query($args, $order, $limit)
	 */
	public function query($args, $order = array(), $limit = array()) {
		$sql = "SELECT * FROM {$this->getSchema()->name} WHERE ";
		//Args is an id
		if (is_numeric($args)) {
			$sql .= "{$this->getSchema()->getIdField()}=$args";
		} else if (is_array($args)) {
			$conditions = array();
			foreach($args as $key => $val) {
				$conditions[] = " ".$key."=" . (is_numeric($val) ? $val : $this->escapeString($val));
			}
			$sql .= implode(' AND ', $conditions);
		} else {
			//no parsible arguments found
			return false;
		}
    
    if (!empty($order) && is_array($order)) {
      $order_clause = array();
      foreach ($order as $fieldname => $direction) {
        $order_clause[] = $fieldname.' '.$direction;
      }
      $sql .= ' ORDER BY '.implode(',', $order_clause);
    }
    
    if ($limit && !empty($limit)) {
      if (is_int($limit)) 
        $sql .= ' LIMIT '.$limit;
      elseif (is_array($limit) && count($limit) == 1)
        $sql .= ' LIMIT '.$limit[0];
      elseif (is_array($limit) && count($limit) == 2)
        $sql .= ' LIMIT '.implode (',', $limit);
      else {
        // something invalid.  do nothing.
      }
    }
    
		$sql .= ';';
		return $this->doQuery($sql);
	}
  
  
  /**
   * Execute raw SQL.  CAUTION: this function does zero escaping or other work.
   * You MUST make sure your query is sanitized before you use this function.
   * @param str $sql
   * @return result 
   */
  public function queryRaw($sql)
  {
    return $this->doQuery($sql);
  }

  
	public function recordExists($id) {
	}
	/**
	 * Getter for $this->schema
	 * @param void
	 * @return SimpleSchema
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function getSchema() 
	{
		return $this->schema;
	} // end function getSchema()
	
	/**
	 * Setter for $this->schema
	 * @param SimpleSchema
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function setSchema($arg0) 
	{
		if (($arg0 instanceOf SimpleSchema) === FALSE)
		{
			throw new DataStoreException('Schema is not an instance of SimpleSchema');
		}
		$this->schema = $arg0;
		return $this;
	} // end function setSchema()

}
