<?php

namespace Http;

/**
 * HTTP Client Class
 * @package Cumula
 * @author Craig Gardner <craig@seabourneconsulting.com>
 **/
class Client extends Base
{
	/**
	 * Constants
	 */
	const HTTP_GET = 'GET';
	const HTTP_POST = 'POST';
	const HTTP_PUT = 'PUT';
	const HTTP_DELETE = 'DELETE';

	/**
	 * Properties
	 */
	/**
	 * URL for the request
	 * @var string
	 **/
	private $url;

	/**
	 * Parameters for the request
	 * @var array
	 **/
	private $params;
	
	/**
	 * Public Methods
	 */
	/*
	 * Class Constructor
	 * @param string $url URL to be requested
	 * @param array $params Array of parameters for the request.
	 * @return void
	 **/
	public function __construct($url, array $params = array()) 
	{
		$this->setUrl($url)->setParams($params);
	} // end function __construct

	/**
	 * Perform a GET request with the current URL and parameters
	 * @param void
	 * @return Http\Response Object
	 **/
	public function get() 
	{
		return $this->performRequest(Client::HTTP_GET);
	} // end function get

	/**
	 * Perform a POST Request with the curent URL and parameters
	 * @param void
	 * @return Http\Response Object
	 **/
	public function post() 
	{
		return $this->performRequest(Client::HTTP_POST);
	} // end function post

	/**
	 * Perform the HTTP Request with the specified method
	 * @param string $method Method to perform the request as
	 * @return Http\Response
	 **/
	private function performRequest($method = Client::HTTP_GET) 
	{

		$ch = curl_init();
		$url_params = http_build_query($this->getParams());
		if ($method == Client::HTTP_POST) {
			$options = array(	
				CURLOPT_URL => $this->getUrl(),
				CURLOPT_POSTFIELDS => $url_params,
			);

		}
		else
		{
			$options = array(
				CURLOPT_URL => $this->getURL() .'?'. $url_params,
				CURLOPT_RETURNTRANSFER => TRUE,
			);

		}

		$options += array(
			CURLOPT_CUSTOMREQUEST => $method,
		);
		
		curl_setopt_array($ch, $options);

		$contents = curl_exec($ch);
		$curlInfo = curl_getinfo($ch);
		$curlInfo['contents'] = $contents;

		curl_close($ch);
		return new Response($curlInfo);
	} // end function performRequest

	/**
	 * Getters and Setters
	 */
	/**
	 * Getter for $this->params
	 * @param void
	 * @return array
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	private function getParams() 
	{
		return $this->params;
	} // end function getParams()
	
	/**
	 * Setter for $this->params
	 * @param array
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	private function setParams($arg0) 
	{
		$this->params = $arg0;
		return $this;
	} // end function setParams()
	
	/**
	 * Getter for $this->url
	 * @param void
	 * @return string
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	private function getUrl() 
	{
		return $this->url;
	} // end function getUrl()
	
	/**
	 * Setter for $this->url
	 * @param string
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	private function setUrl($arg0) 
	{
		$this->url = $arg0;
		return $this;
	} // end function setUrl()
} // end class Client extends Base
