<?php
namespace Http;

/**
 * HTTP Response Class
 * @package Cumula
 * @author Craig Gardner <craig@seabourneconsulting.com>
 **/
class Response extends Base
{
	/**
	 * Properties
	 */
	/**
	 * HTTP Code of the Transfer
	 * @var integer
	 **/
	private $httpCode;

	/**
	 * Getter for $this->httpCode
	 * @param void
	 * @return integer
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function getHttpCode() 
	{
		return $this->httpCode;
	} // end function getHttpCode()
	
	/**
	 * Setter for $this->httpCode
	 * @param integer
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function setHttpCode($arg0) 
	{
		$this->httpCode = $arg0;
		return $this;
	} // end function setHttpCode()

	/**
	 * Content Returned by the request
	 * @var string
	 **/
	private $content;

	/**
	 * Size of the Request
	 * @var integer
	 **/
	private $requestSize;

	/**
	 * Content Type of the request
	 * @var string
	 **/
	private $contentType;

	/**
	 * URL that was requested
	 * @var string
	 **/
	private $url;


	/**
	 * Public Methods
	 */
	/**
	 * Class Constructor
	 * @param array $options Array of options
	 * @return void
	 **/
	public function __construct(array $options = array()) 
	{
		$this->configure($options);
	} // end function __construct

	/**
	 * Magic __toString Method
	 * @param void
	 * @return string
	 **/
	public function __toString() 
	{
		return (string) $this->getContents();
	} // end function __toString
	
	/**
	 * Helper Methods
	 */
	/**
	 * Configure the Response based on a passed array
	 * @param array $options Array of options to configure
	 * @return Http\Response
	 **/
	public function configure(array $options = array()) 
	{
		$methods = get_class_methods(get_class($this));
		foreach ($options as $key => $value) 
		{
			$method = sprintf('set%s', str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
			if (in_array($method, $methods)) 
			{
				$this->$method($value);
			}
		}
	} // end function configure
	
	/**
	 * Getters and Setters
	 */
	/**
	 * Getter for $this->url
	 * @param void
	 * @return string
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function getUrl() 
	{
		return $this->url;
	} // end function getUrl()
	
	/**
	 * Setter for $this->url
	 * @param string
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function setUrl($arg0) 
	{
		$this->url = $arg0;
		return $this;
	} // end function setUrl()
	
	/**
	 * Getter for $this->contentType
	 * @param void
	 * @return string
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function getContentType() 
	{
		return $this->contentType;
	} // end function getContentType()
	
	/**
	 * Setter for $this->contentType
	 * @param string
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function setContentType($arg0) 
	{
		$this->contentType = $arg0;
		return $this;
	} // end function setContentType()
	
	/**
	 * Getter for $this->requestSize
	 * @param void
	 * @return integer
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function getRequestSize() 
	{
		return $this->requestSize;
	} // end function getRequestSize()
	
	/**
	 * Setter for $this->requestSize
	 * @param integer
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function setRequestSize($arg0) 
	{
		$this->requestSize = $arg0;
		return $this;
	} // end function setRequestSize()
	
	/**
	 * Getter for $this->content
	 * @param void
	 * @return string
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function getContents() 
	{
		return $this->content;
	} // end function getContents()
	
	/**
	 * Setter for $this->content
	 * @param string
	 * @return void
	 * @author Craig Gardner <craig@seabourneconsulting.com>
	 **/
	public function setContents($arg0) 
	{
		$this->content = $arg0;
		return $this;
	} // end function setContents()
} // end class Response extends Base
