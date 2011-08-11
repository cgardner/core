<?php

require_once realpath(dirname(__FILE__) .'/BaseSqlDataStore.class.php');
require_once realpath(dirname(__FILE__) .'/Exception/DataStoreException.class.php');

/**
 * PDO Data Store
 * @package Cumula
 * @author Craig Gardner <craig@seabourneconsulting.com>
 **/
abstract class PDODataStore extends BaseSqlDataStore 
{
	/**
	 * Driver Constants
	 */
	const DRIVER_MYSQL = 'mysql';
	const DRIVER_PGSQL = 'pgsql';
	const DRIVER_SQLITE = 'sqlite';
	const DRIVER_ODBC = 'odbc';
	const DRIVER_MSSQL = 'mssql';
	const DRIVER_OCI = 'oci';
	const DRIVER_FIREBIRD = 'firebird';
	const DRIVER_INFORMIX = 'informix';

	/**
	 * Properties
	 */
	/**
	 * Schema Object Used for this DataStore
	 * @var SimpleSchema
	 **/
	private $schema;

	/**
	 * Driver to use for the Connection
	 * @var string
	 **/
	protected $driver;

	/**
	 * PDO Connection Class
	 * @var PDO
	 **/
	protected $pdo;

	/**
	 * User to connect to the database with
	 * @var string
	 **/
	private $user;

	/**
	 * Password used to connect to the dataabase with 
	 * @var string
	 **/
	private $pass;


	/**
	 * Abstract Methods
	 */
	abstract protected function getDSN();

	/**
	 * Public Methods
	 */
	/**
	 * Class Constructor
	 * @param SimpleSchema $schema Schema to use for this DataStore
	 * @param array $config Array of Configuration Options to be set
	 * @return void
	 **/
	public function __construct(SimpleSchema $schema, array $config) 
	{
		$config['schema'] = $schema;
		$this->configure($config);
		$this->connect();
	} // end function __construct

	/**
	 * Connect method
	 * @param void
	 * @return void
	 * @throws DataStoreException
	 **/
	public function connect() 
	{
		try {
			$this->setPDO(new PDO($this->getDsn(), $this->getUser(), $this->getPass()));
			$this->getPDO()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $this;
		}
		catch (PDOException $e) {
			throw new DataStoreException(sprintf('PDO Exception: %s', $e->getMessage()), $e->getCode(), $e);
		}
	} // end function connect

	/**
	 * Configure the DataStore
	 * @param array $config array of configuration options
	 * @return MySQLDataStore
	 **/
	public function configure(array $config) 
	{
		if (count($config) > 0) {
			$methods = get_class_methods($this);
			foreach ($config as $key => $value) 
			{
				$method = sprintf('set%s', str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
				if (in_array($method, $methods))
				{
					$this->$method($value);
				}
			}
		}
		return $this;
	} // end function configure

	/**
	 * Getters and Setters
	 */
	/**
	 * Getter for $this->pdo
	 * @param void
	 * @return PDO
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	protected function getPDO() 
	{
		if (is_null($this->pdo))
		{
			$this->connect();
		}
		return $this->pdo;
	} // end function getPDO()
	
	/**
	 * Setter for $this->pdo
	 * @param PDO
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	protected function setPDO($arg0) 
	{
		$this->pdo = $arg0;
		return $this;
	} // end function setPDO()

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

	/**
	 * Getter for $this->pass
	 * @param void
	 * @return string
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function getPass() 
	{
		return $this->pass;
	} // end function getPass()
	
	/**
	 * Setter for $this->pass
	 * @param string
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function setPass($arg0) 
	{
		$this->pass = $arg0;
		return $this;
	} // end function setPass()
	
	/**
	 * Getter for $this->user
	 * @param void
	 * @return string
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function getUser() 
	{
		return $this->user;
	} // end function getUser()
	
	/**
	 * Setter for $this->user
	 * @param string
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function setUser($arg0) 
	{
		$this->user = $arg0;
		return $this;
	} // end function setUser()
	
} // end class PDODataStore extends BaseSqlDataStore
