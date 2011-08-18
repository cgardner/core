<?php
namespace UserManager;
use Cumula\BaseMVCModel as BaseMVCModel;

class User extends BaseMVCModel {
	public function __construct($args = array()) {
		parent::__construct($args);
	}
	
	public static function setupDataStore() {
		$schema = new SimpleSchema('user_table', 'id', self::getFields());
		return new SqliteDataStore($schema, array('source_directory' => DATAROOT, 'filename' => 'users_db'));
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
