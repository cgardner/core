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
 * BaseDataStore Class
 *
 * Abstract base class for all DataStores.  This class handles the datastore schema installation.
 * 
 * Each datastore must have a schema that describes the fields used by the data object.  Most 
 * importantly, the schema describes the field used as the id for each record in the datastore.
 *
 * @package		Cumula
 * @subpackage	Core
 * @author     Seabourne Consulting
 */
abstract class BaseDataStore extends EventDispatcher {
	protected $_schema;
	
	/**
	 * Constructor
	 * 
	 * @return unknown_type
	 */
	public function __construct() {
		
	}
	
	/**
	 * Sets the schema for use by the datastore.
	 * 
	 * @param $schema
	 * @return unknown_type
	 */
	public function setSchema(CumulaSchema $schema) {
		$this->_schema = $schema;
	}
	
	/**
	 * @return unknown_type
	 */
	public function getSchema() {
		return $this->_schema;
	}
	
	
	/**
	 * Returns the field used as the unique id for records
	 * @return unknown_type
	 */
	protected function _getId() {
		return $this->_schema->idField();
	}
	
	/**
	 * Converts an object to an array of key/value pairs
	 * 
	 * @param $obj
	 * @return unknown_type
	 */
	protected function _objToArray($obj) {
		if(is_array($obj))
			return $obj;
		else
			return (array)$obj;
	}	
	
	/**
	 * Converts an array to a string.
	 * 
	 * @param array $arr
	 * @return unknown_type
	 */
	protected function _arrayToString(array $arr) {
		return implode(" ", $arr);
	}
	
	/**
	 * Function to translate the fields definition into the implementation specific representation.
	 * 
	 * @param $fields
	 * @return unknown_type
	 */
	public function translateFields($fields) {
		return $fields;
	}
	
	/**
	 * Callback run when the data store is installed
	 * 
	 * @return unknown_type
	 */
	public function install() {
		
	}
	
	/**
	 * Callback run when the data store is uninstalled
	 * 
	 * @return unknown_type
	 */
	public function uninstall() {
		
	}
}