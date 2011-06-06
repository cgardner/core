<?php
/**
 *  @package Cumula
 *  @subpackage Core
 *  @version    $Id$
 */

/**
 * The base class representing the HTTP request
 * 
 * @author Mike Reich
 * @package Cumula
 * @subpackage Core
 *
 */
final class Request extends EventDispatcher {
	public $path;
	public $arguments;
	public $requestIp;
	public $params;
	
	public function __construct() {
		parent::__construct();
		$this->init();
	}
	
	protected function init() {
		$this->path = array_key_exists('PATH_INFO', $_SERVER) ? $_SERVER['PATH_INFO'] : '';
		$this->requestIp = $_SERVER['REMOTE_ADDR'];
		$this->params = $_POST;
	}
}