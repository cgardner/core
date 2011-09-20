<?php

require_once 'base/Test.php';
require_once 'components/Cache/Cache.component';
require_once 'components/Stash/Stash.component';

/**
 * Stash Component Test
 * @package Cumula
 * @author Craig Gardner <craig@seabourneconsulting.com>
 **/
class Test_Stash extends Test_BaseTest 
{
	/**
	 * Component
	 * @var \Stash\Stash
	 **/
	private $component;
	/**
	 * setUp
	 **/
	public function setUp() 
	{
		parent::setUp();
		$this->component = new \Stash\Stash();
		\Cache\Cache::getInstance()->startup();
	} // end function setUp

	/**
	 * Test the getUrlStash method
	 * @param void
	 * @return void
	 * @group all
	 * @covers \Stash\Stash::getUrlStash
	 **/
	public function testGetUrlStash() 
	{
		$url = 'http://www.cumula.org';
		$return = $this->component->getUrlStash($url);
		$this->assertInternalType('array', $return);
		$this->assertArrayHasKey('content', $return);
		$this->assertArrayHasKey('headers', $return);
		$this->assertEquals($return['headers']['Stash-From-Cache'], 'no');

		$return2 = $this->component->getUrlStash($url);
		$this->assertEquals($return2['headers']['Stash-From-Cache'], 'yes');
	} // end function testGetUrlStash

	/**
	 * Test the is_url method
	 * @param string $url URL to check
	 * @param boolean $expected Expected Return
	 * @return void	
	 * @group all
	 * @dataProvider isUrlDataProvider
	 * @covers \Stash\Stash::is_url
	 **/
	public function testIsUrl($url, $expected) 
	{
		$this->assertEquals($this->component->is_url($url), $expected);
	} // end function testIsUrl

	/**
	 * Data Provider for testIsUrl()
	 * @param void
	 * @return void
	 **/
	public function isUrlDataProvider() 
	{
		return array(
			'google' => array('http://www.google.com', TRUE),
			'cumula' => array('http://www.cumula.org', TRUE),
			'unidiq' => array(uniqid('badurl_'), FALSE),
		);
	} // end function isUrlDataProvider


	/**
	 * Test the get/set/addStashTable methods
	 * @param void
	 * @return void
	 * @group all
	 **/
	public function testStashTableMethods() 
	{
		$stashName = uniqid('stash_');
		$stashUrl = sprintf('http://www.cumula.org/?_=%s', $stashName);
		$this->component->addStash($stashName, $stashUrl);

		$this->assertEquals($this->component->stashExists($stashName), $stashUrl);
	} // end function testStashTableMethods
} // end class Test_Stash extends Test_BaseTest
