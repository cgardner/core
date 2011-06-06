<?php

/**
 *  @package Cumula
 *  @subpackage Core
 *  @version    $Id$
 */

/**
 * The interface for all templater classes.
 * 
 * @author mike
 *
 */
interface CumulaTemplater {
	
	public function __construct();
	
	public function setTemplateDir($dir);
	
}