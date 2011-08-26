<?php
require_once 'base/Test.php';
require_once 'classes/Autoloader.class.php';

/**
 * Unit Tests for the Cumula Autoloader
 * @package Cumula
 * @author Craig Gardner <craig@seabourneconsulting.com>
 **/
class Test_Autoloader extends Test_BaseTest 
{
	/**
	 * setUp
	 * @param void
	 * @return void
	 **/
	public function setUp() 
	{
		parent::setUp();

		\Cumula\Autoloader::setup();
	} // end function setUP

	/**
	 * Make sure the Autoloader was registered
	 * @param void
	 * @return void
	 * @group all
	 * @covers Cumula\Autoloader::setup
	 **/
	public function testAutoloaderRegistered() 
	{
		\Cumula\Autoloader::setup();
		$functions = spl_autoload_functions();
		$this->assertTrue(in_array(array('Cumula\\Autoloader', 'load'), $functions));
	} // end function testAutoloaderRegistered
} // end class Test_Autoloader extends Test_BaseTest
