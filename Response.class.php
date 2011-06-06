<?php
/**
 *  @package Cumula
 *  @subpackage Core
 *  @version    $Id$
 */

/**
 * The base class representing the response.
 * 
 * @author Mike Reich
 * @package Cumula
 * @subpackage Core
 *
 */
class Response extends EventDispatcher {
	/**
	 * The raw response object, including any headers, the content and the status code
	 * 
	 * @var array
	 */
	public $response = array('headers' => array(), 
							   'content' => '',
							   'status_code' => 200,
							   'data' => array());
	
	/**
	 * Constructor
	 * 
	 * @return unknown_type
	 */
	public function __construct() {
		parent::__construct();
		$this->addEventListenerTo('Application', BOOT_SHUTDOWN, 'send');
		$this->addEvent(RESPONSE_PREPARE);
		$this->addEvent(RESPONSE_SEND);
	}
	
	/**
	 * Dispatches the response to the browser
	 * 
	 * @return unknown_type
	 */
	public function send() {
		$this->dispatch(RESPONSE_PREPARE);
		$this->sendRawResponse($this->response['headers'], $this->response['content'], $this->response['status_code']);
		$this->dispatch(RESPONSE_SEND);
	}
	
	public function send404() {
		$this->response['status_code'] = 404;
	}
	
	public function send405() {
		$this->response['status_code'] = 405; //Change Denied
		$this->response['headers']['Pragma'] = 'no-cache';
		$this->sendRawResponse($this->response['headers'], 'Invalid Change', $this->response['status_code']);
		exit;
	}
	
	public function send302($url) {
		$this->response['headers']['Location'] = $url;
		$this->response['status_code'] = 302;
	}
	
	/**
	 * Sends the raw response to the browser.
	 * 
	 * @param $headers array An array of key value pairs of header: values
	 * @param $body string The body of the response
	 * @param $code integer The HTTP response code
	 * @return unknown_type
	 */
	public function sendRawResponse($headers, $body, $code) {
		if ($code == 404) {
			header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
			echo $body;
			return;
		}
		foreach($headers as $key => $value) {
			$this->_sendHeader($key, $value, $code);
		}
		echo $body;
	}
	
	private function _sendHeader($header, $value, $status_code = null) {
		header("$header: $value", true, $status_code);
	}
}