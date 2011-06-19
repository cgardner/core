<?php
//TODO: Figure out what todo with this, whether to keep it.
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
 * WriteOnlyTextDataStore Class
 *
 * The WriteOnlyTextDataStore allows for writing of arbitrary data to a text file.
 *
 * @package		Cumula
 * @subpackage	Logger
 * @author     Seabourne Consulting
 */
class WriteOnlyTextDataStore extends BaseDataStore implements CumulaDataStore {
	private $_logFile;

	public function __construct($config_values) {
		$this->_storage = array();
		$this->_logFile = $config_values['logfile'];
	}

	public function connect() {
		return true;
	}

	public function disconnect() {
		return true;
	}

	public function create($obj) {
		@file_put_contents($this->_logFile, $this->_arrayToString($this->_objToArray($obj)), FILE_APPEND);
		@file_put_contents($this->_logFile, "\n", FILE_APPEND);
	}

	public function update($obj) {
		return false;
	}

	public function createOrUpdate($obj) {
		$this->create($obj);
	}

	public function delete($obj) {
		return false;
	}

	public function query($args, $order = null, $sort = null) {
		return false;
	}

	public function recordExists($id) {
		return false;
	}
}