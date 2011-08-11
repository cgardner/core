<?php
require_once realpath(dirname(__FILE__) .'/../../classes/PDODataStore.class.php');

/**
 * MySQL Data Store
 *
 * Implementation of DataStore that uses a MySQL backend to save data
 *
 * @package Cumlula
 * @subpackage CoreData
 * @author Seabourne Consulting
 **/
class MySQLDataStore extends PDODataStore 
{
	/**
	 * Properties
	 */
	
	/**
	 * Host used to connect to the data store with
	 * @var string
	 **/
	private $host;

	/**
	 * Port to use to connect to the database
	 * @var integer
	 **/
	private $port;
	
	/**
	 * Database to use for the connection
	 * @var string
	 **/
	private $db;

	/**
	 * Public Methods
	 */
	/**
	 * Implementation of translateFields
	 * @param array $fields Array of fields to translate
	 * @return array array of translated fields
	 **/
	public function translateFields($fields) 
	{
		$return = array();
		foreach ($fields as $field => $args) 
		{
			switch($args['type']) 
			{
				case self::FIELD_TYPE_STRING:
					$type = 'VARCHAR';
					break;

				case self::FIELD_TYPE_INTEGER:
					$type = 'INT';
					break;

				case self::FIELD_TYPE_BLOB:
					$type = 'BLOB';
					break;

				case self::FIELD_TYPE_DATETIME:
					$type = 'DATETIME';
					break;

				case self::FIELD_TYPE_FLOAT:
					$type = 'FLOAT';
					break;

				case self::FIELD_TYPE_TEXT:
					$type = 'TEXT';
					break;

				case self::FIELD_TYPE_BOOL:
					$type = 'BOOL';
					break;

				default:
					throw new DataStoreException(sprintf('%s is an invalid data type', $args['type']));
			}
			$new_args = array(
				'type' => $type,
			);

			if (isset($args['default'])) 
			{

				$new_args['default'] = sprintf('DEFAULT %s', is_numeric($args['default']) ? $args['default'] : "'{$args['default']}'";
			}

			if (isset($args['autoincrement'])) 
			{
				$new_args['autoincrement'] = 'AUTO_INCREMENT NOT NULL UNIQUE';		
			}

			if (isset($args['primary']))
			{
					$new_args['primary'] = 'PRIMARY_KEY';
			}

			// If the field is set as autoincrement, it should already be set as NOT NULL
			if (isset($args['null']) && !isset($new_args['autoincrement']))
			{
				$new_args['null'] = 'NOT NULL';
			}

			$return[$field] = $new_args;
		}
		return $return;
	} // end function translateFields

	/**
	 * Get the DSN for the PDO Connection
	 * @param void
	 * @return void
	 **/
	public function getDSN() 
	{
		return sprintf('mysql:host=%s;dbname=%s;port=%s', $this->getHost(), $this->getDb(), $this->getPort());
		
	} // end function getDSN

	/**
	 * Getters and Setters
	 */
	/**
	 * Getter for $this->db
	 * @param void
	 * @return string
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function getDb() 
	{
		return $this->db;
	} // end function getDb()
	
	/**
	 * Setter for $this->db
	 * @param string
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function setDb($arg0) 
	{
		$this->db = $arg0;
		return $this;
	} // end function setDb()
	
	/**
	 * Getter for $this->port
	 * @param void
	 * @return integer
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function getPort() 
	{
		if (is_null($this->port)) 
		{
			$this->setPort(3306);
		}
		return $this->port;
	} // end function getPort()
	
	/**
	 * Setter for $this->port
	 * @param integer
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function setPort($arg0) 
	{
		$this->port = $arg0;
		return $this;
	} // end function setPort()
	
	/**
	 * Getter for $this->host
	 * @param void
	 * @return string
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function getHost() 
	{
		return $this->host;
	} // end function getHost()
	
	/**
	 * Setter for $this->host
	 * @param string
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function setHost($arg0) 
	{
		$this->host = $arg0;
		return $this;
	} // end function setHost()
} // end class MySQLDataStore extends PDODataStore
