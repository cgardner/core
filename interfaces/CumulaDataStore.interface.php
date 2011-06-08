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
 * CumulaDataStore Interface
 *
 * Describes the basic CRUD functions for all DataStores.
 *
 * @package		Cumula
 * @subpackage	Core
 * @author     Seabourne Consulting
 */
interface CumulaDataStore {
	/**
	 * Takes an object
	 * 
	 * @param $obj
	 * @return unknown_type
	 */
	public function update($obj);
	
	public function delete($obj);
	
	public function create($obj);
	
	public function query($args, $order = null, $sort = null);
	
	public function recordExists($id);
	
	public function connect();
	
	public function disconnect();
	
	public function install();
	
	public function uninstall();
	
	public function translateFields($fields);
	
	public function __construct($config_values);
}