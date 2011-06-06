<?php
/**
 *  @package Cumula
 *  @subpackage Core
 *  @version    $Id$
 */

/**
 * The content block represents output content displayed by Cumula.
 * 
 * @author mike
 *
 */
class ContentBlock extends EventDispatcher {
	public $data;
	public $content;
	
	public function __construct() {
		parent::__construct();
		$this->data = array();
	}
	
	public function render() {
		return $this->content;
	}
	
	public function __toString() {
		return $this->render();
	}
}