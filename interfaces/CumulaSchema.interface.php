<?php
/**
 *  @package Cumula
 *  @subpackage Core
 *  @version    $Id$
 */

interface CumulaSchema {	
	/**
	 * Returns an array of key value pairs describing the fields associated with the object stored in the schema.
	 * @return unknown_type
	 */
	public function getFields();
	
	public function setFields($fields);
	
	/**
	 * Defines the unique id field used by the schema
	 * @return unknown_type
	 */
	public function getIdField();
	
	public function setIdField($idField);
}      