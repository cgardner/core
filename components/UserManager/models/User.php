<?php

class User extends BaseMVCModel {
	public function __construct($args = array()) {
		parent::__construct($args);
	}
	
	public static function getDataStore() {
		$fields = self::getFields();
		$schema = new BaseSchema();
		$schema->setFields($fields);
		$schema->setIdField('id');
		$schema->name = 'user_table';
		$dataStore = new SqliteDataStore(array('source_directory' => DATAROOT, 'filename' => 'users_db', 'schema' => $schema));
		$dataStore->connect();
		return $dataStore;
	}
	
	public static function setupFields() {
		self::addField('id', 'integer', array('autoincrement' => true));
		self::addField('token', 'string', array('size' => 255));
		self::addField('email', 'string', array('size' => 255));
		self::addField('password', 'string', array('size' => 255));
		self::addField('created_on', 'datetime');
		self::addField('last_logged_in', 'datetime');
	}
}