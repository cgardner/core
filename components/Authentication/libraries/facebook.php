<?php

class facebookAuthentication extends Authentication implements CumulaAuth
{
  protected $success = FALSE;
  
  /**
   * @var array - Populated with the response
   */
  protected $response = array();

  /**
   * @param $params array of auth params
   * @return array response from auth service
   */
	public function authenticate($params)
  {
    $this->response = array('not implemented yet');
    return $this->response;
  }
  
  /**
   * Getter for $success
   */
  public function success()
  {
    return $this->success;
  }

  
}