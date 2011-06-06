<?php
/**
 *  @package Cumula
 *  @subpackage Core
 *  @version    $Id$
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